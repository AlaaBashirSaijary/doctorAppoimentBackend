<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EmergencyAcceptedNotification;
use Illuminate\Http\Request;
use App\Models\EmergencyAppointment;

class EmergencyController extends Controller
{
    public function requestEmergency(Request $request)
    {
        $request->validate([
            'patient_condition' => 'required|string',
            'severity_level' => 'required|string',
            'specialization' => 'required|string|exists:specializations,name',
        ]);
        $emergency = EmergencyAppointment::create([
            'patient_id' => Auth::id(),
            'patient_condition' => $request->patient_condition,
            'severity_level' => $request->severity_level,  // إضافة مستوى الشدة
            'specialization' => $request->specialization,
            'is_accepted' => false,
            'status' => 'pending',  // تحديد الحالة إلى "قيد الانتظار"
            'requested_at' => now(),  // إضافة وقت تقديم الطلب
        ]);
        $notificationController = new NotificationController();
        $notificationController->sendNotification(
            $emergency->patient_id,
            'طلب طوارئ قيد المراجع',
            'تم إرسال طلب الطوارئ الخاص بك وهو قيد المراجع ' . Auth::user()->name .
            ' on ' . $emergency .
            ' at ' . $emergency . ' has been المراجع.'
        );
        return response()->json(['message' => 'Emergency request submitted', 'emergency' => $emergency], 201);
    }
    public function acceptEmergency($id)
    {
        $emergency = EmergencyAppointment::findOrFail($id);
        $doctor = Auth::user();

        if ($emergency->is_accepted) {
            return response()->json(['message' => 'Emergency already accepted by another doctor'], 400);
        }
        //dd( $emergency->specialization);
        if ($doctor->specialization !== $emergency->specialization) {
            return response()->json([
                'message' => 'You are not qualified to handle this emergency. Required specialization: ' . $emergency->specialization
            ], 403);
        }
        // منع الطبيب من قبول أكثر من حالة طوارئ في نفس الوقت
        $existingEmergency = EmergencyAppointment::where('doctor_id', $doctor->id)
            ->where('is_accepted', true)
            ->where('status', 'completed')  // التأكد من أن الطبيب لا يقبل حالات طوارئ أخرى
            ->first();

        if ($existingEmergency) {
            return response()->json(['message' => 'You already have an active emergency appointment'], 400);
        }

        // تحديث الطلب
        $emergency->update([
            'doctor_id' => $doctor->id,
            'is_accepted' => true,
            'status' => 'completed',  // تحديث الحالة إلى "مقبول"
            'accepted_at' => now(),  // إضافة وقت قبول الطلب
        ]);

        // إرسال إشعار للمريض
      //  $emergency->patient->notify(new EmergencyAcceptedNotification($doctor, $emergency));
      $notificationController = new NotificationController();
        $notificationController->sendNotification(
            $emergency->patient_id,
            'accept emergency quesrt',
            'accept your request by doctor ' . $doctor->name . '.',
            ' on ' . $emergency .
            ' at ' . $emergency . ' has been المراجع.'
        );
        
        return response()->json(['message' => 'Emergency appointment accepted successfully', 'emergency' => $emergency]);
    }
    public function getAvailableDoctors(Request $request)
{
    $request->validate([
        'specialization' => 'required|string|exists:specializations,name'
    ]);

    $availableDoctors =User::role('doctor')
        ->where('specialization', $request->specialization)
        ->whereNotIn('id', function ($query) {
            $query->select('doctor_id')->from('emergency_appointments')->where('is_accepted', true);
        })
        ->get();

    return response()->json([
        'available_doctors' => $availableDoctors
    ]);
}

}
