<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\PrescriptionRequest ;
use App\Mail\PrescriptionMail;
use Illuminate\Support\Facades\Mail;
class PrescriptionController extends Controller
{
    // عرض جميع الوصفات الطبية
    public function index()
    {
        $prescriptions = Prescription::all();
        return response()->json($prescriptions);
    }

    // عرض وصفة طبية معينة
    public function show($id)
    {
        $prescription = Prescription::findOrFail($id);
        return response()->json($prescription);
    }

    // إنشاء وصفة طبية جديدة
    public function store(PrescriptionRequest  $request)
    {
        $prescription = Prescription::create($request->all());
        if ($prescription->sent_to_patient) {
            Mail::to($prescription->patient->email)->send(new PrescriptionMail($prescription));
        }
        return response()->json($prescription, 201);
    }

    // تحديث وصفة طبية
    public function update(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id',
            'patient_id' => 'required|exists:users,id',
            'medication_details' => 'required|string',
            'instructions' => 'required|string',
            'prescription_date' => 'nullable|date',
            'status' => 'required|in:completed,pending,cancelled',
            'prescription_type' => 'required|in:medical,cosmetic,Herbal medicine recipe',
            'doctor_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $prescription->update($request->all());
        return response()->json($prescription);
    }

    // حذف وصفة طبية
    public function destroy($id)
    {
        $prescription = Prescription::findOrFail($id);
        $prescription->delete();
        return response()->json(['message' => 'Prescription deleted successfully']);
    }
    public function patientPrescriptions(Request $request)
{
    // جلب الوصفات الخاصة بالمريض (المستخدم الحالي)
    $prescriptions = Prescription::where('patient_id', $request->user()->id)->get();

    return response()->json($prescriptions);
}
public function resendPrescriptionEmail($id)
{
    $prescription = Prescription::findOrFail($id);

    // التحقق من أن الوصفة الطبية تم إرسالها من قبل
    if (!$prescription->sent_to_patient) {
        // إرسال الوصفة عبر البريد الإلكتروني
        Mail::to($prescription->patient->email)->send(new PrescriptionMail($prescription));

        // تحديث حالة الإرسال
        $prescription->update(['sent_to_patient' => true]);

        return response()->json(['message' => 'Prescription resent successfully']);
    }

    return response()->json(['message' => 'Prescription has already been sent'], 400);
}
public function checkPrescriptionStatus($id)
{
    $prescription = Prescription::findOrFail($id);
    return response()->json(['status' => $prescription->status]);
}
public function cancelPrescription($id)
{
    $prescription = Prescription::findOrFail($id);

    // التحقق مما إذا كانت الوصفة في حالة يمكن إلغاؤها
    if ($prescription->status == 'completed') {
        return response()->json(['error' => 'Cannot cancel a completed prescription'], 400);
    }

    $prescription->update(['status' => 'cancelled']);

    return response()->json(['message' => 'Prescription cancelled successfully']);
}
public function showUnsentPrescriptions()
{
    $unsentPrescriptions = Prescription::where('sent_to_patient', false)->get();
    return response()->json($unsentPrescriptions);
}
public function searchPrescriptions(Request $request)
{
    $query = Prescription::query();

    if ($request->has('patient_id')) {
        $query->where('patient_id', $request->patient_id);
    }

    if ($request->has('prescription_date')) {
        $query->whereDate('prescription_date', $request->prescription_date);
    }

    $prescriptions = $query->get();

    return response()->json($prescriptions);
}

}
