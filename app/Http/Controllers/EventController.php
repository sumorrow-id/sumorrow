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
    public function store(Request $request, Community $community): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            // Prefix 'storage/' so the URL works directly in asset($event->image_url)
            $imagePath = 'storage/'.$path;
        }

        $community->events()->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'image_url' => $imagePath,
        ]);

        return back()->with('success', 'Event created successfully!');
    }

    public function destroy(Event $event): RedirectResponse
    {
        // Only the event creator or the community owner may delete it.
        abort_unless(
            $event->user_id === Auth::id() || $event->community->created_by === Auth::id(),
            403
        );

        if ($event->image_url) {
            Storage::disk('public')->delete(Str::after($event->image_url, 'storage/'));
        }

        $event->delete();

        return back()->with('success', 'Event deleted successfully!');
    }
}
