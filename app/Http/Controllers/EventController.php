<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Admins can see all events, regular users only see their own
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin') {
            $query = Event::query();
        } else {
            $query = auth()->user()->events();
        }

        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('client_name', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%')
                  ->orWhere('event_date', 'like', '%' . $search . '%');
            });
        }

        $events = $query->get();
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'event_date' => 'required|date',
            'location' => 'required',
            'description' => 'nullable',
            'client_name' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
        }

        auth()->user()->events()->create($request->only(['name', 'description', 'event_date', 'location', 'client_name']) + ['image' => $imagePath]);

        return redirect()->route('events.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Admins can access all events, regular users only their own
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin') {
            $event = Event::findOrFail($id);
        } else {
            $event = auth()->user()->events()->findOrFail($id);
        }
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Admins can access all events, regular users only their own
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin') {
            $event = Event::findOrFail($id);
        } else {
            $event = auth()->user()->events()->findOrFail($id);
        }
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Admins can access all events, regular users only their own
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin') {
            $event = Event::findOrFail($id);
        } else {
            $event = auth()->user()->events()->findOrFail($id);
        }
        $request->validate([
            'name' => 'required',
            'event_date' => 'required|date',
            'location' => 'required',
            'description' => 'nullable',
            'client_name' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $imagePath = $event->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('events', 'public');
        }

        $event->update($request->only(['name', 'description', 'event_date', 'location', 'client_name']) + ['image' => $imagePath]);
        return redirect()->route('events.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Admins can access all events, regular users only their own
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin') {
            $event = Event::findOrFail($id);
        } else {
            $event = auth()->user()->events()->findOrFail($id);
        }
        $event->delete();
        return redirect()->route('events.index');
    }
}
