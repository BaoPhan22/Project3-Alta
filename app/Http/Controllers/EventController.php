<?php

namespace App\Http\Controllers;

use App\Models\Events;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function event()
    {
        $events = Events::all();
        return view('events.event', compact('events'));
    }

    public function EventDetail(int $id)
    {
        $event = Events::where('id', $id)->first();
        return view('events.event_detail', compact('event'));
    }
}
