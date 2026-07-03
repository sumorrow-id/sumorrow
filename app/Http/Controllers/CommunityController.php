<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostTag;
use App\Models\User;
use Illuminate\Http\Request;
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
        $postsQuery = Post::with(['user', 'tags', 'images', 'likes', 'saves'])->latest();

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

        // ----------------------------------------------------------------
        // 5. Who to Follow (required by sidebar.blade.php component)
        // ----------------------------------------------------------------
        $whoToFollow = User::when(Auth::check(), function ($query) {
                $query->where('id', '!=', Auth::id());
            })
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return view('community.index', compact(
            'myCommunities',
            'suggestedCommunities',
            'posts',
            'popularTags',
            'forumLeaders',
            'whoToFollow',
            'activeTag'
        ));
    }
    
    public function show(Community $community)
    {
        // 1. Ambil member dari komunitas ini (asumsi ada relasi 'users' di model Community)
        // Kita ambil 3 member teratas untuk avatar stack, dan hitung totalnya
        $community->load(['members' => function($query) {
            $query->latest()->take(3);
        }]);

        // Hitung total member real di database
        $membersCount = $community->members()->count();

        // Gunakan inRandomOrder() agar rekomendasi gunung selalu segar
        $recommendedMountains = \App\Models\Mountain::inRandomOrder()->limit(5)->get();

        // 2. Ambil postingan khusus milik komunitas ini secara real dari database
        $posts = \App\Models\Post::where('community_id', $community->id)
            ->with(['user', 'tags', 'images', 'likes', 'saves']) // Sesuaikan dengan relasi di model Post kamu
            ->latest()
            ->paginate(10);

        // 3. Lempar semua data real ke view
        return view('community.show', compact('community', 'posts', 'membersCount', 'recommendedMountains'));
    }



    public function join(Community $community)
    {
        $user = Auth::user();

        if (!$community->isMember($user)) {
            $community->members()->attach($user->id, ['role' => 'member']);
            
            return redirect()->route('community', ['tab' => 'community'])
                            ->with('success', 'Welcome to ' . $community->name . '!');
        }

        return redirect()->route('community', ['tab' => 'community'])
                            ->with('info', 'You are already a member of this community.');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255|unique:communities,name',
            'description' => 'required|string',
            'privacy' => 'required|in:public,private',
        ]);

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to create a community.');
        }

        $community = Community::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'privacy' => $request->privacy,
            'image_url' => 'https://via.placeholder.com/150',
            'banner_url' => 'https://via.placeholder.com/150',
            'created_by' => Auth::id(),    
        ]);

        $community->members()->attach(Auth::id(), ['role' => 'admin']);

        return redirect()->route('community.show', $community->id);

        // return redirect()->route('community', ['tab' => 'community'])
                            // ->with('success', $community->name . ' created successfully!');
    }

    public function updateImage(Request $request, Community $community)
    {
        // 1. Validasi input
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_type' => 'required|in:profile,banner'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // Membuat nama file unik, misal: community_1_profile_1719734400.jpg
            $filename = 'community_' . $community->id . '_' . $request->image_type . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke folder public/images/community
            $file->move(public_path('images/community'), $filename);
            $pathPath = '/images/community/' . $filename;

            // 2. Simpan path-nya ke kolom database yang sesuai
            if ($request->image_type === 'profile') {
                // Hapus file lama jika ada dan bukan gambar default
                if ($community->image_url && file_exists(public_path($community->image_url))) {
                    @unlink(public_path($community->image_url));
                }
                $community->image_url = $pathPath;
            } else {
                // Jika ada kolom banner_url di tabel community kamu
                if ($community->banner_url && file_exists(public_path($community->banner_url))) {
                    @unlink(public_path($community->banner_url));
                }
                $community->banner_url = $pathPath;
            }

            $community->save();

            return back()->with('success', 'Community ' . ucfirst($request->image_type) . ' updated successfully!');
        }

        return back()->with('error', 'Failed to upload image.');
    }

    public function leave(Community $community)
    {
        $user = Auth::user();

        $user->communities()->detach($community->id);

        return redirect()->route('community', ['tab' => 'community'])
                            ->with('info', 'You are not a member of this community.');
    }
}
