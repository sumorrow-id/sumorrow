<?php

namespace App\Http\Controllers;
use App\Models\Community;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function store(Request $request, Community $community)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        dd($request->all());

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Menyimpan file dan mendapatkan path-nya
            $path = $request->file('image')->store('events', 'public');
            // Menambahkan prefix 'storage/' agar bisa langsung dipakai di src="{{ asset($event->image_url) }}"
            $imagePath = 'storage/' . $path;
        }
        
        $community->events()->create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'image_url' => $imagePath,
        ]);

        return back()->with('success', 'Event created successfully!');
    }
}
