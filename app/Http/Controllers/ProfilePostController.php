<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfilePostRequest;
use App\Models\Mountain;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfilePostController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Hanya catatan puncak (summit log) — post forum ditandai dengan tags
        $posts = $user->posts()
            ->whereDoesntHave('tags')
            ->with(['mountain.province', 'images' => function ($query) {
                $query->orderBy('position', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('profile.posts.index', compact('posts'));
    }

    public function create(): View
    {
        $mountains = Mountain::orderBy('name')->get();

        return view('profile.posts.create', compact('mountains'));
    }

    public function show(int $id): View
    {
        /** @var User $user */
        $user = Auth::user();

        $post = $user->posts()
            ->with(['mountain.province', 'images' => function ($query) {
                $query->orderBy('position', 'asc');
            }])->findOrFail($id);

        return view('profile.posts.show', compact('post'));
    }

    public function store(StoreProfilePostRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $post = $user->posts()->create($request->safe()->only(
            'title', 'body', 'mountain_id', 'climbing_date', 'duration_days'
        ));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('posts', 'public');
                $post->images()->create([
                    'image_url' => $path,
                    'position' => $index,
                ]);
            }
        }

        return redirect()->route('profile.posts.index')->with('success', __('profile.activity_posted'));
    }
}
