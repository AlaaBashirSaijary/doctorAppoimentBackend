<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorSignUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:8',
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|unique:users|max:255',
            'certificate' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The doctor\'s name is required.',
            'email.required' => 'The email address is required.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'The password is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'specialization.required' => 'The doctor\'s specialization is required.',
            'license_number.required' => 'The license number is required.',
            'license_number.unique' => 'This license number is already registered.',
            'certificate.required' => 'The doctor\'s certificate is required.',
            'certificate.mimes' => 'The certificate must be a file of type: PDF, JPG, or PNG.',
            'certificate.max' => 'The certificate size must not exceed 2 MB.',
        ];
    }
}
