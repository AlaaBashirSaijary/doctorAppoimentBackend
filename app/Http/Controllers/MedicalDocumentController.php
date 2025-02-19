<?php

namespace App\Http\Controllers;

use App\Models\MedicalDocument;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MedicalDocumentRequest;
class MedicalDocumentController extends Controller
{
    // رفع مستند طبي جديد
    public function uploadDocument(MedicalDocumentRequest $request)
    {
       $patient = Auth::user(); // الحصول على المريض

        // رفع الملف إلى التخزين
        $filePath = $request->file('file')->store('medical_documents', 'public');

        // تخزين البيانات في قاعدة البيانات
        $document = MedicalDocument::create([
            'patient_id' => $patient->id,
            'document_type' => $request->document_type,
            'file_path' => $filePath,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'document' => $document
        ]);
    }

    // عرض المستندات الخاصة بالمريض
    public function getPatientDocuments()
    {
        $patient = Auth::user();
        $documents = MedicalDocument::where('patient_id', $patient->id)->get();

        return response()->json([
            'documents' => $documents
        ]);
    }

    // مشاركة المستند مع طبيب عند الحجز
    public function shareDocumentWithDoctor($documentId, $doctorId)
    {
        $document = MedicalDocument::findOrFail($documentId);
        $doctor = User::findOrFail($doctorId);
        return response()->json([
            'message' => 'Document shared with doctor successfully',
            'document' => $document,
            'doctor' => $doctor
        ]);
    }
}
