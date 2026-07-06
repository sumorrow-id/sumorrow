<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinCommunityRequest;
use App\Http\Requests\StoreCommunityRequest;
use App\Http\Requests\UpdateCommunityRequest;
use App\Models\Community;
use App\Models\Mountain;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function join(JoinCommunityRequest $request, Community $community): RedirectResponse
    {
        $user = Auth::user();

        if ($community->isMember($user)) {
            return redirect()->route('community.show', $community)
                ->with('info', __('community.already_a_member'));
        }

        // Private communities are token-gated; the creator may always rejoin.
        if ($community->privacy === 'private' && ! $community->isCreatedBy($user)) {
            if (! hash_equals((string) $community->join_token, trim($request->join_token))) {
                return back()
                    ->withErrors(['join_token' => __('community.invalid_token')])
                    ->withInput();
            }
        }

        $community->members()->attach($user->id, [
            'role' => $community->isCreatedBy($user) ? 'admin' : 'member',
        ]);

        return redirect()->route('community.show', $community)
            ->with('success', __('community.joined_community', ['name' => $community->name]));
    }

    public function store(StoreCommunityRequest $request): RedirectResponse
    {
        $community = Community::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'privacy' => $request->privacy,
            'join_token' => $request->privacy === 'private' ? Str::upper(Str::random(8)) : null,
            'image_url' => null,
            'created_by' => Auth::id(),
        ]);

        $community->members()->attach(Auth::id(), ['role' => 'admin']);

        return redirect()->route('community.show', $community)
            ->with('success', __('community.community_created', ['name' => $community->name]));
    }

    public function show(Community $community): View
    {
        // Private communities hide their content behind a join-token gate;
        // the creator can always get back in.
        $user = Auth::user();
        if ($community->privacy === 'private' && ! $community->isMember($user) && ! $community->isCreatedBy($user)) {
            return view('community.show-locked', compact('community'));
        }

        // Full member list feeds the Members tab.
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

    public function update(UpdateCommunityRequest $request, Community $community): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'privacy' => $validated['privacy'],
        ];

        if ($validated['privacy'] === 'private' && ! $community->join_token) {
            $data['join_token'] = Str::upper(Str::random(8));
        }

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

    public function destroy(Community $community): RedirectResponse
    {
        abort_unless(Auth::user()->can('delete', $community), 403);

        $this->deleteImage($community->image_url);
        $this->deleteImage($community->banner_url);

        // Memberships and community posts are removed by FK cascade.
        // ponytail: image files of cascaded posts stay on disk; sweep them if storage matters.
        $community->delete();

        return redirect()->route('community', ['tab' => 'community'])
            ->with('success', __('community.community_deleted'));
    }

    public function leave(Community $community): RedirectResponse
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
