<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Mountain;
use App\Models\Post;
use App\Models\PostTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        // Forum feed only: summit logs carry no category tags, so exclude
        // tag-less posts — same convention as PostController::index. Posts
        // made inside a community stay on their community page.
        $postsQuery = Post::with(['author', 'tags', 'images', 'likes'])
            ->whereNull('community_id')
            ->whereHas('tags')
            ->latest();

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
        // Rank by forum posts only — summit logs (tag-less posts) don't count.
        $forumLeaders = User::withCount(['posts as posts_count' => function ($query) {
            $query->whereHas('tags');
        }])
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

        return redirect()->route('community.show', $community)
            ->with('success', __('community.community_created', ['name' => $community->name]));
    }

    public function show(Community $community)
    {
        // Full member list feeds the Members tab; the header avatar stack
        // takes the first few in the view.
        $community->load([
            'members',
            'creator',
            'events' => fn ($query) => $query->with('user')->orderBy('event_date'),
        ]);

        $posts = Post::where('community_id', $community->id)
            ->with(['author', 'tags', 'images', 'likes'])
            ->latest()
            ->paginate(10);

        $recommendedMountains = Mountain::with('images')->inRandomOrder()->limit(5)->get();

        return view('community.show', compact('community', 'posts', 'recommendedMountains'));
    }

    public function update(Request $request, Community $community)
    {
        abort_unless($community->isCreatedBy(Auth::user()), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:communities,name,'.$community->id,
            'description' => 'required|string',
            'privacy' => 'required|in:public,private',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:4096',
        ]);

        $data = [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'privacy' => $validated['privacy'],
        ];

        if ($request->hasFile('profile_image')) {
            $data['image_url'] = $this->replaceImage($community->image_url, $request->file('profile_image'));
        }

        if ($request->hasFile('banner_image')) {
            $data['banner_url'] = $this->replaceImage($community->banner_url, $request->file('banner_image'));
        }

        $community->update($data);

        return redirect()->route('community.show', $community)
            ->with('success', __('community.community_updated'));
    }

    public function destroy(Community $community)
    {
        abort_unless($community->isCreatedBy(Auth::user()), 403);

        $this->deleteImage($community->image_url);
        $this->deleteImage($community->banner_url);

        // Memberships and community posts are removed by FK cascade.
        // ponytail: image files of cascaded posts stay on disk; sweep them if storage matters.
        $community->delete();

        return redirect()->route('community', ['tab' => 'community'])
            ->with('success', __('community.community_deleted'));
    }

    public function leave(Community $community)
    {
        $user = Auth::user();

        $user->communities()->detach($community->id);

        return redirect()->route('community', ['tab' => 'community'])
            ->with('info', __('community.left_community'));
    }

    /**
     * Store a newly uploaded community image and delete the one it replaces.
     */
    private function replaceImage(?string $oldUrl, UploadedFile $file): string
    {
        $this->deleteImage($oldUrl);

        return 'storage/'.$file->store('community', 'public');
    }

    /**
     * Delete an uploaded community image; bundled defaults live outside
     * storage/ so they are never touched.
     */
    private function deleteImage(?string $url): void
    {
        if ($url && str_starts_with($url, 'storage/')) {
            Storage::disk('public')->delete(Str::after($url, 'storage/'));
        }
    }
}
