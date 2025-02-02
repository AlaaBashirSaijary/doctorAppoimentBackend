<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AppointmentBooked;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    // عرض المواعيد المتاحة لدكتور معين
    public function getAvailableAppointments($doctorId)
    {
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->where('is_available', true)
            ->get();

        return response()->json(['appointments' => $appointments]);
    }

    // إنشاء مواعيد متاحة لدكتور
    public function createAvailableAppointments(Request $request)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $appointment = Appointment::create([
            'doctor_id' => Auth::id(),
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'is_available' => true,
        ]);

        return response()->json(['message' => 'Appointment created successfully', 'appointment' => $appointment]);
    }

    // حجز موعد من قبل مريض
    public function bookAppointment($appointmentId)
{
    // البحث عن الموعد باستخدام الـ ID
    $appointment = Appointment::find($appointmentId);

    // التحقق من وجود الموعد وكونه متاحًا
    if (!$appointment || !$appointment->is_available) {
        return response()->json(['error' => 'Appointment not available'], 404);
    }

    // التحقق من أن الموعد مخصص للطبيب الحالي (الذي يخص المريض)
    $doctorId = $appointment->doctor_id;
    $patientId = Auth::id(); // معرف المريض (المستخدم الذي قام بتسجيل الدخول)

    // تحديث الموعد ليكون محجوزًا بواسطة المريض
    $appointment->update([
        'patient_id' => $patientId,
        'is_available' => false,
    ]);
    Notification::send($appointment->doctor, new AppointmentBooked($appointment));
    $notificationController = new NotificationController();
    $notificationController = new NotificationController();
    $notificationController->sendNotification(
        $doctorId,
        'Appointment Booked',
        'A patient has booked an appointment with you for ' . $appointment->appointment_date . ' at ' . $appointment->appointment_time
    );

    return response()->json([
        'message' => 'Appointment booked successfully',
        'appointment' => [
            'id' => $appointment->id,
            'doctor_id' => $doctorId,
            'patient_id' => $patientId,
            'appointment_date' => $appointment->appointment_date,
            'appointment_time' => $appointment->appointment_time,
            'is_available' => $appointment->is_available,
        ]
    ]);
}

    // عرض مواعيد الطبيب (متاحة وغير متاحة)
    public function getDoctorAppointments()
    {
        $appointments = Appointment::where('doctor_id', Auth::id())->get();
        return response()->json(['appointments' => $appointments]);
    }
}

