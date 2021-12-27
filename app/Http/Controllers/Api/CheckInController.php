<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkin;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function index(Request $request)
    {
        $locations = Checkin::where('customer_id', $request->user()->id)
            ->latest()->limit(20)->get();

        return ['status' => true, 'data' => $locations];
    }

    public function save(Request $request)
    {
        $checkin = Checkin::create([
            'customer_id' => $request->user()->id,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return ['status' => true, 'data' => $checkin, 'message' => 'Checkin saved successfully.'];
    }

    public function delete(Request $request)
    {
        $checkin = Checkin::where(['customer_id' => $request->user()->id, 'id' => $request->id])->first();

        if ($checkin)
            $checkin->delete();

        return ['status' => !!$checkin, 'message' => $checkin ? 'Checkin delete successfully.' : 'Error occurred.'];
    }
}
