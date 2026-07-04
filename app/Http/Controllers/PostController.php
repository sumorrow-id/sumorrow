<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Allowed forum category tags.
     * Defined as a constant so validation and the view dropdown
     * always stay in sync from a single source of truth.
     */
    public const CATEGORY_TAGS = [
        'Hiking Stories',
        'Tips and Trick',
        'Gear & Equipment',
        'Safety & Survival',
    ];

    // ====================================================================
    // INDEX — Explore feed
    // ====================================================================

    /**
     * Display the Explore feed.
     * Display the Explore feed.
     */
    public function index(Request $request)
    {
        $activeTag = $request->query('tag');
        $search = $request->query('search');

        // ----------------------------------------------------------------
        // 1. Main Feed Query
        // Only real forum posts belong here. Summit logs (created from the
        // profile) carry no category tags, so exclude tag-less posts — same
        // convention ProfileController uses to split the two.
        $postsQuery = Post::with(['author', 'tags', 'images', 'likes'])
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
        $popularTags = PostTag::select('keyword')
            ->selectRaw('COUNT(DISTINCT post_id) as post_count')
            ->groupBy('keyword')
            ->orderByDesc('post_count')
            ->limit(8)
            ->get();

        // ----------------------------------------------------------------
        // 3. Forum Leaders
        // ----------------------------------------------------------------
        // Rank by forum posts only — summit logs (tag-less posts) don't count.
        $forumLeaders = User::withCount(['posts as posts_count' => function ($query) {
            $query->whereHas('tags');
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
    public function show(Post $post)
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
    public function store(Request $request)
    {
        $request->validate([
            // A post must have at least a body, images, OR a gif
            'body' => 'nullable|required_without_all:images,gif_url|string|max:5000',
            'category_tags' => 'required|array|min:1',
            'category_tags.*' => ['string', 'in:'.implode(',', self::CATEGORY_TAGS)],
            'images' => 'nullable|required_without_all:body,gif_url|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'gif_url' => 'nullable|url|max:2048',
        ], [
            'body.required_without_all' => __('community.validation_post_content'),
            'images.required_without_all' => __('community.validation_post_content'),
            'category_tags.required' => __('community.validation_category_tags'),
            'images.*.image' => __('community.validation_image'),
            'images.*.max' => __('community.validation_image_size'),
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Create the post
        $post = $user->posts()->create([
            'title' => '',
            'body' => $request->body ?? '',
            'gif_url' => $request->gif_url,
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

        return redirect()
            ->route('community.explore')
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
    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

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
    public function destroy(Request $request, Post $post)
    {
        abort_unless($post->author_id === Auth::id(), 403);

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
    public function toggleLike(Request $request, Post $post)
    {
        $post->likes()->toggle(auth()->id());

        return response()->json([
            'liked' => $post->likes()->where('user_id', auth()->id())->exists(),
            'count' => $post->likes()->count(),
        ]);
    }
}
