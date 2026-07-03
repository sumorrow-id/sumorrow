<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        // ----------------------------------------------------------------
        // 1. My Communities & Suggested Communities
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

        // ----------------------------------------------------------------
        // 2. Main Feed (required by feed.blade.php component)
        // ----------------------------------------------------------------
        $activeTag = $request->query('tag');
        $postsQuery = Post::with(['author', 'tags', 'images', 'likes'])->latest();

        if ($activeTag) {
            $postsQuery->whereHas('tags', function ($query) use ($activeTag) {
                $query->where('keyword', strtolower(trim($activeTag)));
            });
        }

        $posts = $postsQuery->paginate(10)->withQueryString();

        // ----------------------------------------------------------------
        // 3. Popular Tags (required by sidebar.blade.php component)
        // ----------------------------------------------------------------
        $popularTags = PostTag::select('keyword')
            ->selectRaw('COUNT(DISTINCT post_id) as post_count')
            ->groupBy('keyword')
            ->orderByDesc('post_count')
            ->limit(8)
            ->get();

        // ----------------------------------------------------------------
        // 4. Forum Leaders (required by sidebar.blade.php component)
        // ----------------------------------------------------------------
        $forumLeaders = User::withCount('posts')
            ->orderByDesc('posts_count')
            ->limit(5)
            ->get();

        return view('community.index', compact(
            'myCommunities',
            'suggestedCommunities',
            'posts',
            'popularTags',
            'forumLeaders',
            'activeTag'
        ));
    }

    public function join(Community $community)
    {
        $user = Auth::user();

        if (! $community->isMember($user)) {
            $community->members()->attach($user->id, ['role' => 'member']);

            return redirect()->route('community', ['tab' => 'community'])
                ->with('success', __('community.joined_community', ['name' => $community->name]));
        }

        return redirect()->route('community', ['tab' => 'community'])
            ->with('info', __('community.already_a_member'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:communities,name',
            'description' => 'required|string',
            'privacy' => 'required|in:public,private',
        ]);

        $community = Community::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'privacy' => $request->privacy,
            'image_url' => null,
            'created_by' => Auth::id(),
        ]);

        $community->members()->attach(Auth::id(), ['role' => 'admin']);

        return redirect()->route('community', ['tab' => 'community'])
            ->with('success', __('community.community_created', ['name' => $community->name]));
    }

    public function leave(Community $community)
    {
        $user = Auth::user();

        $user->communities()->detach($community->id);

        return redirect()->route('community', ['tab' => 'community'])
            ->with('info', __('community.left_community'));
    }
}
