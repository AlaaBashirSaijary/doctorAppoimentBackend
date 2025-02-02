<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    // عرض جميع مواعيد الحجز للمريض
    public function getPatientAppointments()
    {
        // الحصول على المريض المتصل حالياً
        $patientId = Auth::id();  // استخدام Auth للتأكد من الحصول على المريض الحالي

        // البحث عن مواعيد الحجز التي تخص المريض المتصل
        $appointments = Appointment::where('patient_id', $patientId)->get();

        // إذا لم يتم العثور على مواعيد
        if ($appointments->isEmpty()) {
            return response()->json(['message' => 'No appointments found for this patient.'], 404);
        }

        // إرجاع المواعيد الخاصة بالمريض
        return response()->json([
            'appointments' => $appointments
        ]);
    }
    public function cancelAppointment($appointmentId)
    {
        $patientId = Auth::id();
        $appointment = Appointment::where('id', $appointmentId)
                                   ->where('patient_id', $patientId)
                                   ->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found or does not belong to this patient.'], 404);
        }

        $doctorId = $appointment->doctor_id;

        // تحديث بيانات الموعد ليصبح متاحًا مرة أخرى
        $appointment->update([
            'patient_id' => null,
            'is_available' => true,
        ]);

        // إرسال إشعار للطبيب
        $notificationController = new NotificationController();
        $notificationController->sendNotification(
            $doctorId,
            'Appointment Cancelled',
            'The appointment scheduled for ' . $appointment->appointment_date .
            ' at ' . $appointment->appointment_time . ' has been cancelled by the patient.'
        );

        return response()->json(['message' => 'Appointment cancelled successfully.']);
    }

}

