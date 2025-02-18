<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    // عرض جميع الأطباء
    public function getAllDoctors()
    {
        $doctors = User::role('doctor')->get();
        return response()->json([
            'doctors' => $doctors
        ]);
    }

    // البحث عن دكتور بالاسم أو الاختصاص
    public function searchDoctors(Request $request)
    {
        // الحصول على القيم المدخلة للبحث
        $query = $request->input('query');

        // البحث عن الأطباء باستخدام الاسم أو التخصص
        $doctors = User::role('doctor')
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%$query%")
                  ->orWhere('specialization', 'LIKE', "%$query%");
            })
            ->get();

        return response()->json([
            'doctors' => $doctors
        ]);
    }
       // عرض جميع المرضى عند الطبيب المتصل
       public function getPatientsForDoctor()
       {
           // الحصول على الطبيب المتصل حالياً
           $doctorId = Auth::id();  // استخدام Auth للتأكد من الحصول على الطبيب الحالي

           // البحث عن الطبيب باستخدام ID
           $doctor = User::find($doctorId);

           // تحقق من وجود الطبيب المتصل
           if (!$doctor || !$doctor->hasRole('doctor')) {
               return response()->json(['error' => 'Doctor not found'], 404);
           }

           // الحصول على المرضى الذين لديهم مواعيد مع هذا الطبيب
           $appointments = Appointment::where('doctor_id', $doctorId)
               ->whereNotNull('patient_id')  // التأكد من أن المريض تم تحديده
               ->get();

           // استخراج بيانات المرضى من المواعيد
           $patients = $appointments->map(function($appointment) {
               return [
                   'patient_id' => $appointment->patient_id,
                   'patient_name' => $appointment->patient->name,  // التأكد من وجود العلاقة بين Appointment و User (المريض)
                   'appointment_date' => $appointment->appointment_date,
                   'appointment_time' => $appointment->appointment_time,
               ];
           });

           return response()->json([
               'doctor' => $doctor->name,
               'patients' => $patients
           ]);
       }
       public function cancelAppointmentByDoctor($appointmentId)
       {
           $doctorId = Auth::id(); // الطبيب المتصل حالياً

           // العثور على الموعد الذي يخص الطبيب الحالي
           $appointment = Appointment::where('id', $appointmentId)
               ->where('doctor_id', $doctorId)
               ->first();

           if (!$appointment) {
               return response()->json(['error' => 'Appointment not found or does not belong to this doctor.'], 404);
           }

           $patientId = $appointment->patient_id;

           // تحديث بيانات الموعد ليصبح متاحًا مرة أخرى
           $appointment->update([
               'patient_id' => null,
               'is_available' => true,
           ]);

           // إرسال إشعار للمريض إذا كان الموعد محجوزًا
           if ($patientId) {
               $notificationController = new NotificationController();
               $notificationController->sendNotification(
                   $patientId,
                   'Appointment Cancelled',
                   'Your appointment with Dr. ' . Auth::user()->name .
                   ' on ' . $appointment->appointment_date .
                   ' at ' . $appointment->appointment_time . ' has been cancelled.'
               );
           }

           return response()->json(['message' => 'Appointment cancelled successfully.']);
       }
       public function updateHomeVisitAvailability(Request $request)
    {
    $doctor = Auth::user();

    // التحقق مما إذا كان المستخدم دكتور
    if (!$doctor->hasRole('doctor')) { // إذا كنت تستخدم Spatie Laravel Permission
        return response()->json(['message' => 'Unauthorized: Only doctors can update this setting'], 403);
    }

    // تحديث إمكانية الزيارة المنزلية
    $doctor->update([
        'can_visit_home' => $request->can_visit_home,
    ]);

    return response()->json(['message' => 'Home visit availability updated successfully']);
     }

}
