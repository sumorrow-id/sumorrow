<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\Community;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
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
    public function store(StoreEventRequest $request, Community $community): RedirectResponse
    {
        $validated = $request->validated();

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
     * Delete an event. Allowed for its creator or the community owner —
     * but only while they are still a member; leaving the community
     * suspends the right until they rejoin.
     */
    public function destroy(Event $event): RedirectResponse
    {
        abort_unless(Auth::user()->can('delete', $event), 403);

        if ($event->image_url) {
            Storage::disk('public')->delete(Str::after($event->image_url, 'storage/'));
        }

        $event->delete();

        return redirect()->route('community.show', [$event->community_id, 'tab' => 'events'])
            ->with('success', __('community.event_deleted'));
    }
}
