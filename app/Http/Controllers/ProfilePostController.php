<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilePostController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $posts = $user->posts()
            ->with(['mountain.province', 'images' => function ($query) {
                $query->orderBy('position', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('profile.posts.index', compact('posts'));
    }

    public function create()
    {
        $mountains = Mountain::orderBy('name')->get();
        return view('profile.posts.create', compact('mountains'));
    }

    public function show($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $post = $user->posts()
            ->with(['mountain.province', 'images' => function ($query) {
                $query->orderBy('position', 'asc');
            }])->findOrFail($id);

        return view('profile.posts.show', compact('post'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'mountain_id' => 'nullable|exists:mountains,id',
            'climbing_date' => 'nullable|date',
            'duration_days' => 'nullable|integer|min:1',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:2048', // Enforce 2MB matching PHP default max file size
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $post = $user->posts()->create($request->only(
            'title', 'body', 'mountain_id', 'climbing_date', 'duration_days'
        ));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('posts', 'public');
                $post->images()->create([
                    'image_url' => $path,
                    'position' => $index
                ]);
            }
        }

        return redirect()->route('profile.posts.index')->with('success', 'Activity posted!');
    }
}
