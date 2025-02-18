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
   public function bookAppointment(Request $request, $appointmentId)
{
    // التحقق من صحة البيانات المدخلة
    $request->validate([
        'patient_name' => 'required|string|max:255',
        'patient_health_status' => 'required|string',
    ]);

    // البحث عن الموعد باستخدام الـ ID
    $appointment = Appointment::find($appointmentId);

    // التحقق من وجود الموعد وكونه متاحًا
    if (!$appointment || !$appointment->is_available) {
        return response()->json(['error' => 'Appointment not available'], 404);
    }

    // جلب معلومات المريض
    $patientId = Auth::id(); // معرف المريض (المستخدم المسجل الدخول)

    // تحديث بيانات الموعد ليصبح محجوزًا من قبل المريض
    $appointment->update([
        'patient_id' => $patientId,
        'patient_name' => $request->patient_name,
        'patient_health_status' => $request->patient_health_status,
        'is_available' => false,
    ]);

    // إرسال إشعار للطبيب عبر Laravel Notifications
    Notification::send($appointment->doctor, new AppointmentBooked($appointment));

    // إرسال إشعار مخصص للطبيب
    $notificationController = new NotificationController();
    $notificationController->sendNotification(
        $appointment->doctor_id,
        'Appointment Booked',
        'A patient (' . $request->patient_name . ') has booked an appointment for ' . $appointment->appointment_date .
        ' at ' . $appointment->appointment_time . '. Health status: ' . $request->patient_health_status
    );

    return response()->json([
        'message' => 'Appointment booked successfully',
        'appointment' => [
            'id' => $appointment->id,
            'doctor_id' => $appointment->doctor_id,
            'patient_id' => $patientId,
            'patient_name' => $request->patient_name,
            'patient_health_status' => $request->patient_health_status,
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
    // في AppointmentController
public function completeAppointment(Request $request, $appointmentId)
{
    $appointment = Appointment::find($appointmentId);

    if (!$appointment || $appointment->is_available) {
        return response()->json(['error' => 'Appointment not available or already completed'], 404);
    }

    // تحديث حالة المعاينة إلى completed
    $appointment->update([
        'consultation_status' => 'completed', // أو no_prescription إذا لم يكن يحتاج الوصفة الطبية
    ]);

    // التحقق من أن المريض يحتاج إلى وصفة طبية
    if ($request->needs_prescription) {
        // إنشاء الوصفة الطبية
        $prescriptionController = new PrescriptionController();
        $prescriptionData = [
            'doctor_id' => Auth::id(),
            'patient_id' => $appointment->patient_id,
            'medication_details' => $request->medication_details,
            'instructions' => $request->instructions,
            'prescription_type' => $request->prescription_type,
            'status' => 'completed',
        ];
        $prescriptionRequest = new \App\Http\Requests\PrescriptionRequest($prescriptionData);
        $prescriptionController = new PrescriptionController();
        $prescriptionController->store($prescriptionRequest);
    }

    return response()->json(['message' => 'Appointment completed successfully']);
}

}

