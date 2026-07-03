<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    // INDEX — Forum feed
    // ====================================================================

    public function index(Request $request)
    {
        $activeTag = $request->query('tag');
        $search    = $request->query('search');

        // ----------------------------------------------------------------
        // 1. Main Feed Query
        $postsQuery = Post::query()->whereNull('community_id')->with(['user', 'tags', 'images', 'likes', 'saves']);

        if (Auth::check()) {
            $postsQuery->withExists(['saves as is_saved' => function ($query) {
                $query->where('user_id', Auth::id());
            }])->orderByDesc('is_saved');
        }

        $postsQuery->latest();

        if ($activeTag) {
            $postsQuery->whereHas('tags', function ($query) use ($activeTag) {
                $query->where('keyword', strtolower(trim($activeTag)));
            });
        }

        if ($search) {
            $postsQuery->where('body', 'like', '%' . $search . '%');
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
        $forumLeaders = User::withCount('posts')
            ->orderByDesc('posts_count')
            ->limit(5)
            ->get();

        // ----------------------------------------------------------------
        // 4. Who to Follow
        // ----------------------------------------------------------------
        $whoToFollow = User::when(Auth::check(), function ($query) {
                $query->where('id', '!=', Auth::id())
                      ->whereDoesntHave('followers', function ($q) {
                          $q->where('follower_id', Auth::id());
                      });
            })
            ->inRandomOrder()
            ->limit(5)
            ->get();

        // ----------------------------------------------------------------
        // 5. My Communities & Suggested Communities
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
            'whoToFollow',
            'activeTag',
            'search',
            'myCommunities',
            'suggestedCommunities'
        ));
    }

    // Tambahkan method ini di PostController.php untuk komunitas
    // public function indexByCommunity($communityId)
    // {
    //     // Hanya ambil post yang milik komunitas ini
    //     $posts = Post::where('community_id', $communityId)
    //                 ->with(['user', 'tags', 'images'])
    //                 ->latest()
    //                 ->get();
                    
    //     return view('community.show', compact('posts', 'communityId'));
    // }

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
            'user',
            'tags',
            'images',
            'comments.user',   // thread replies with their users
        ]);

        $comments      = $post->comments;
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
     *
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // A post must have at least a body, images, OR a gif
            'body'            => 'nullable|required_without_all:images,gif_url|string|max:5000',
            'category_tags'   => 'nullable|array',
            'category_tags.*' => ['string', 'in:' . implode(',', self::CATEGORY_TAGS)],
            'images'          => 'nullable|required_without_all:body,gif_url|array|max:10',
            'images.*'        => 'image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'gif_url'         => 'nullable|url|max:2048',
            'community_id'    => 'nullable|exists:communities,id',
        ], [
            'body.required_without_all'   => 'Please write something, attach an image, or select a GIF before posting.',
            'images.required_without_all' => 'Please write something, attach an image, or select a GIF before posting.',
            'images.*.image'              => 'One or more files are not valid images.',
            'images.*.max'                => 'Each image must be smaller than 4 MB.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Create the post
        $post = $user->posts()->create([
            'title'        => '',
            'body'         => $request->body ?? '',
            'gif_url'      => $request->gif_url,
            // Jika community_id dari request kosong, simpan sebagai null secara eksplisit
            'community_id' => $request->filled('community_id') ? $request->community_id : null,
        ]);

        // Attach category tags klo ada
        if ($request->has('category_tags')) {
            foreach ($request->category_tags as $tag) {
                $post->tags()->create(['keyword' => $tag]);
            }
        }

        // Store every uploaded image and attach to the post
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $position => $file) {
                $path = $file->store('posts', 'public');

                $post->images()->create([
                    'image_url' => 'storage/' . $path,
                    'position'  => $position + 1,   // 1-indexed
                ]);
            }
        }
        if ($request->has('community_id') && $request->community_id) {
            return redirect()->route('community.show', $request->community_id)
                     ->with('success', 'Your post has been published to the community!');
        }
        return redirect()
            ->route('community.forum')
            ->with('success', 'Your post has been published!');
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

        /** @var \App\Models\User $user */
        $user = Auth::user();

        PostComment::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body'    => $request->body,
        ]);

        return redirect()
            ->route('community.posts.show', $post->id)
            ->with('success', 'Reply posted!');
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

    // ====================================================================
    // TOGGLE SAVE / BOOKMARK
    // ====================================================================

    /**
     * Toggle a bookmark on a post.
     *
     * Route: POST /community/posts/{post}/save
     * Name:  community.posts.save
     */
    public function toggleSave(Request $request, Post $post)
    {
        $post->saves()->toggle(auth()->id());

        return response()->json([
            'saved' => $post->saves()->where('user_id', auth()->id())->exists(),
        ]);
    }

    public function destroy($id)
    {
        $post = \App\Models\Post::findOrFail($id);
        // Cek apakah user adalah pemilik post
        if (auth()->id() !== (int)$post->user_id) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Hapus gambar terkait
        if ($post->images) {
            foreach ($post->images as $image) {
                Storage::delete(str_replace('/storage/', '', $image->image_url));
            }
        }

        $post->delete();

        // Logika Redirect Pintar:
        // Jika post punya community_id, kembali ke forum komunitas tersebut
        if ($post->community_id) {
            return redirect()->route('community.show', $post->community_id)
                            ->with('success', 'Post deleted successfully.');
        }

        // Jika tidak, kembali ke forum global
        return redirect()->route('community.index')->with('success', 'Post deleted successfully.');
    }
}
