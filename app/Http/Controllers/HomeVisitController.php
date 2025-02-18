<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomeVisit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\HomeVisitAcceptedNotification;

class HomeVisitController extends Controller
{
    public function requestHomeVisit(Request $request)
    {
        $request->validate([
            'patient_condition' => 'required|string',
            'address' => 'required|string|max:255',
        ]);

        $homeVisit = HomeVisit::create([
            'patient_id' => Auth::id(),
            'patient_condition' => $request->patient_condition,
            'address' => $request->address,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return response()->json(['message' => 'Home visit request submitted', 'home_visit' => $homeVisit], 201);
    }
    public function acceptHomeVisit($id)
    {
        $homeVisit = HomeVisit::findOrFail($id);
        $doctor = Auth::user();

        if (!$doctor->can_visit_home) {
            return response()->json(['message' => 'You are not allowed to accept home visits'], 403);
        }
        if ($homeVisit->status !== 'pending') {
            return response()->json(['message' => 'This home visit request is no longer available'], 400);
        }

        // تحديث الطلب
        $homeVisit->update([
            'doctor_id' => $doctor->id,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // إرسال إشعار للمريض
        //$homeVisit->patient->notify(new HomeVisitAcceptedNotification($doctor, $homeVisit));
        $notificationController = new NotificationController();
        $notificationController->sendNotification(
            $homeVisit->patient_id,
            'accept home visit',
            'accept home visit  by doctor ' . Auth::user()->name .
            ' on ' . $homeVisit .
            ' at ' . $homeVisit . ' has been cancelled.'
        );
        return response()->json(['message' => 'Home visit request accepted', 'home_visit' => $homeVisit]);
    }
}
