<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    public function index()
    {
        // 1. Ambil komunitas yang diikuti oleh user saat ini (My Communities)
        // Jika user belum login, kita ambil komunitas default atau kosongkan
        $myCommunities = collect();
        if (Auth::check()) {
            $myCommunities = Auth::user()->communities()->withCount('members')->get();
        } else {
            // Fallback kalau user guest: tampilkan semua komunitas sebagai sampel
            $myCommunities = collect();
        }

        // 2. Ambil rekomendasi komunitas (Suggested For You)
        // Kita ambil komunitas publik yang belum diikuti oleh user
        $myCommunityIds = $myCommunities->pluck('id')->toArray();
        $suggestedCommunities = Community::whereNotIn('id', $myCommunityIds)
            ->withCount('members')
            ->take(6)
            ->get();

        // Kirim kedua variabel ke view
        return view('community.index', compact('myCommunities', 'suggestedCommunities'));
    }

    public function join(Community $community)
    {
        $user = Auth::user();

        // Cek apakah user sudah bergabung atau belum, biar tidak dobel
        if (!$community->isMember($user)) {
            // Menggunakan relasi belongsToMany yang sudah dibuat timmu untuk attach user baru sebagai 'member'
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
            'image_url' => 'https://via.placeholder.com/150', // Placeholder image, bisa diganti dengan upload nanti
            'created_by' => Auth::id(),    
        ]);

        $community->members()->attach(Auth::id(), ['role' => 'admin']);

        return redirect()->route('community', ['tab' => 'community'])
                            ->with('success', $community->name . ' created successfully!');
    }

    public function leave(Community $community)
    {
        $user = Auth::user();

        $user->communities()->detach($community->id);

        return redirect()->route('community', ['tab' => 'community'])
                            ->with('info', 'You are not a member of this community.');
    }
}
