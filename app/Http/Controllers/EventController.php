<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Store a new community event. Members only.
     *
     * Validation uses the named "event" error bag so the events modal on the
     * community page can be told apart from the edit-community modal.
     */
    public function store(Request $request, Community $community): RedirectResponse
    {
        abort_unless($community->isMember(Auth::user()), 403);

        $validated = $request->validateWithBag('event', [
            'title' => 'required|string|max:255',
            'event_date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = 'storage/'.$request->file('image')->store('events', 'public');
        }

        $community->events()->create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'event_date' => $validated['event_date'],
            'location' => $validated['location'],
            'image_url' => $imagePath,
        ]);

        return redirect()->route('community.show', [$community, 'tab' => 'events'])
            ->with('success', __('community.event_created'));
    }

    /**
     * Delete an event. Allowed for its creator or the community owner.
     */
    public function destroy(Event $event): RedirectResponse
    {
        abort_unless(
            $event->user_id === Auth::id() || $event->community->isCreatedBy(Auth::user()),
            403
        );

        if ($event->image_url) {
            Storage::disk('public')->delete(Str::after($event->image_url, 'storage/'));
        }

        $event->delete();

        return redirect()->route('community.show', [$event->community_id, 'tab' => 'events'])
            ->with('success', __('community.event_deleted'));
    }
}
