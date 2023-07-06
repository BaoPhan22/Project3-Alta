<?php

namespace App\Http\Controllers;

use App\Models\Events;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function test()
    {
        $url = Cloudinary::getUrl('image_2_mzjbow');
        // return $url;
        dd($url);
    }
    //* api route
    public function index()
    {
        $events = Events::all();
        return response()->json($events);
    }
    public function detail($id)
    {
        $event = Events::where('id', $id)->first();
        return response()->json($event);
    }
    public function update(Request $request, $id)
    {
        $event = Events::where('id', $id)->update([
            'name' => $request->name,
            'location' => $request->location,
            'start' => $request->start,
            'end' => $request->end,
            'price' => $request->price,
            'detail' => $request->detail,
            'imgUrl' => $request->imgUrl,
        ]);
        return response()->json($event);
    }
    public function store(Request $request)
    {
    }
    //* api route

    //* web route
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
    //* web route
}
