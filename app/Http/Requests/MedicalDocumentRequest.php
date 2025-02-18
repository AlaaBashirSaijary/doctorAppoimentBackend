<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'document_type' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,png,doc,docx|max:2048', // تحقق من نوع الملف
            'description' => 'nullable|string|max:255',
        ];
    }
    public function messages()
    {
        return [
            'document_type.required' => 'The document type is required.',
            'document_type.string' => 'The document type must be a string.',
            'file.required' => 'The file is required.',
            'file.file' => 'The file must be a valid file.',
            'file.mimes' => 'The file must be a pdf, jpg, png, doc, or docx file.',
            'file.max' => 'The file must not be larger than 2MB.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description must not exceed 255 characters.',
        ];
    }
}
