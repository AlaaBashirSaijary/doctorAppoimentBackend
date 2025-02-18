<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrescriptionRequest extends FormRequest
{
    public function authorize()
    {
        return true;  // تأكد من إضافة التحقق من الصلاحيات إذا لزم الأمر
    }

    public function rules()
    {
        return [
            'doctor_id' => 'required|exists:users,id',
            'patient_id' => 'required|exists:users,id',
            'medication_details' => 'required|string',
            'instructions' => 'required|string',
            'prescription_date' => 'nullable|date',
            'status' => 'required|in:completed,pending,cancelled',
            'prescription_type' => 'required|in:medical,cosmetic,Herbal medicine recipe',
            'doctor_notes' => 'nullable|string',
        ];
    }
}
