<?php

namespace App\Http\Controllers;

use App\Models\Events;
use Carbon\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // public function test()
    // {
    //     $url = Cloudinary::getUrl('image_2_mzjbow');
    //     // return $url;
    //     dd($url);
    // }
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
        $uploadedFileUrl = 'no image';
        if (isset($_FILES['image'])) {
            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
        }
        Events::create([
            'name' => $request->name,
            'location' => $request->location,
            'start' => $request->start,
            'end' => $request->end,
            'price' => $request->price,
            'detail' => $request->detail,
            'imgUrl' => $uploadedFileUrl,
        ]);
    }
    //* api route

    //* web route
    public function event()
    {
        $today = new Carbon();
        $events = Events::where('end', '>', $today)->orderBy('start', 'asc')->limit(4)->get();
        return view('events.event', compact('events'));
        // dd($today);
    }

    public function EventDetail(int $id)
    {
        $event = Events::where('id', $id)->first();
        return view('events.event_detail', compact('event'));
    }
    //* web route
}
