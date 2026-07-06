<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostCommentRequest;
use App\Http\Requests\StorePostRequest;
use App\Models\Community;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostTag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    // ====================================================================
    // INDEX — Explore feed
    // ====================================================================

    /**
     * Display the Explore feed.
     * Display the Explore feed.
     */
    public function index(Request $request): View
    {
        $activeTag = $request->query('tag');
        $search = $request->query('search');

        // ----------------------------------------------------------------
        // 1. Main Feed Query
        // Only real forum posts belong here. Summit logs (created from the
        // profile) carry no category tags, so exclude tag-less posts — same
        // convention ProfileController uses to split the two.
        // Community posts stay on their community page, not the global feed.
        $postsQuery = Post::with(['author', 'tags', 'images', 'likes'])
            ->whereNull('community_id')
            ->whereHas('tags');

        $postsQuery->latest();

        if ($activeTag) {
            $postsQuery->whereHas('tags', function ($query) use ($activeTag) {
                $query->where('keyword', strtolower(trim($activeTag)));
            });
        }

        if ($search) {
            $postsQuery->where('body', 'like', '%'.$search.'%');
        }

        $posts = $postsQuery->paginate(10)->withQueryString();

        // ----------------------------------------------------------------
        // 2. Popular Tags
        // ----------------------------------------------------------------
        // Global forum only — tags on community posts don't count.
        $popularTags = PostTag::select('keyword')
            ->selectRaw('COUNT(DISTINCT post_id) as post_count')
            ->whereHas('post', fn ($query) => $query->whereNull('community_id'))
            ->groupBy('keyword')
            ->orderByDesc('post_count')
            ->limit(8)
            ->get();

        // ----------------------------------------------------------------
        // 3. Forum Leaders
        // ----------------------------------------------------------------
        // Rank by global forum posts only — summit logs (tag-less posts)
        // and posts made inside a community don't count.
        $forumLeaders = User::withCount(['posts as posts_count' => function ($query) {
            $query->whereNull('community_id')->whereHas('tags');
        }])
            ->orderByDesc('posts_count')
            ->limit(5)
            ->get();

        // ----------------------------------------------------------------
        // 4. My Communities & Suggested Communities
        // ----------------------------------------------------------------
        $myCommunities = collect();
        if (Auth::check()) {
            $myCommunities = Auth::user()->communities()->withCount('members')->get();
        }

        $myCommunityIds = $myCommunities->pluck('id')->toArray();
        $suggestedCommunities = Community::whereNotIn('id', $myCommunityIds)
            ->withCount('members')
            ->take(6)
            ->get();

        return view('community.index', compact(
            'posts',
            'popularTags',
            'forumLeaders',
            'activeTag',
            'search',
            'myCommunities',
            'suggestedCommunities'
        ));
    }

    // ====================================================================
    // SHOW — Post detail / thread page
    // ====================================================================

    /**
     * Display a single post with its comment thread.
     *
     * Route: GET /community/posts/{post}
     */
    public function show(Post $post): View
    {
        $post->load([
            'author',
            'tags',
            'images',
            'comments.user',   // thread replies with their authors
        ]);

        $comments = $post->comments;
        $commentsCount = $comments->count();

        return view('community.post-detail', compact(
            'post',
            'comments',
            'commentsCount'
        ));
    }

    // ====================================================================
    // STORE — Save a new post from the Explore feed composer
    // ====================================================================

    /**
     * Store a new forum post.
     *
     * Route:  POST /community/posts
     * Name:   community.posts.store
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Posting inside a community is members-only
        $community = null;
        if ($request->filled('community_id')) {
            $community = Community::findOrFail($request->integer('community_id'));
            abort_unless($community->isMember($user), 403);
        }

        // Create the post
        $post = $user->posts()->create([
            'title' => '',
            'body' => $request->body ?? '',
            'gif_url' => $request->gif_url,
            'community_id' => $community?->id,
        ]);

        // Attach category tags
        foreach ($request->category_tags as $tag) {
            $post->tags()->create(['keyword' => $tag]);
        }

        // Store every uploaded image and attach to the post
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $position => $file) {
                $path = $file->store('posts', 'public');

                $post->images()->create([
                    'image_url' => 'storage/'.$path,
                    'position' => $position + 1,   // 1-indexed
                ]);
            }
        }

        return redirect($community ? route('community.show', $community) : route('community.explore'))
            ->with('success', __('community.post_published'));
    }

    // ====================================================================
    // STORE COMMENT — Save a comment on a specific post
    // ====================================================================

    /**
     * Store a new comment on a post.
     *
     * Route: POST /community/posts/{post}/comments
     * Name:  community.posts.comments.store
     * Guard: auth middleware
     */
    public function storeComment(StorePostCommentRequest $request, Post $post): RedirectResponse
    {
        abort_unless($request->user()->can('interact', $post), 403);

        /** @var User $user */
        $user = Auth::user();

        PostComment::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' => $request->body,
        ]);

        return redirect()
            ->route('community.posts.show', $post->id)
            ->with('success', __('community.reply_posted'));
    }

    // ====================================================================
    // DESTROY — Delete an own post (forum post or summit log)
    // ====================================================================

    /**
     * Delete a post owned by the authenticated user, including its
     * uploaded image files. Related rows (tags, images, comments, likes)
     * are removed by database cascade.
     *
     * Route: DELETE /community/posts/{post}
     * Name:  community.posts.destroy
     */
    public function destroy(Request $request, Post $post): RedirectResponse
    {
        abort_unless($request->user()->can('delete', $post), 403);

        foreach ($post->images as $image) {
            if (! str_contains($image->image_url, 'http')) {
                // image_url is stored either as "posts/x.jpg" or "storage/posts/x.jpg"
                Storage::disk('public')->delete(Str::after($image->image_url, 'storage/'));
            }
        }

        $post->delete();

        // Deleting from the summit-log detail page: back() would return to the
        // now-deleted post and 404, so send the user to their activities list.
        if ($request->boolean('redirect_to_activities')) {
            return redirect()->route('profile.posts.index')->with('success', __('community.post_deleted'));
        }

        return back()->with('success', __('community.post_deleted'));
    }

    // ====================================================================
    // TOGGLE LIKE
    // ====================================================================

    /**
     * Toggle a like on a post.
     *
     * Route: POST /community/posts/{post}/like
     * Name:  community.posts.like
     */
    public function toggleLike(Request $request, Post $post): JsonResponse
    {
        abort_unless($request->user()->can('interact', $post), 403);

        $post->likes()->toggle(auth()->id());

        return response()->json([
            'liked' => $post->likes()->where('user_id', auth()->id())->exists(),
            'count' => $post->likes()->count(),
        ]);
    }
}
