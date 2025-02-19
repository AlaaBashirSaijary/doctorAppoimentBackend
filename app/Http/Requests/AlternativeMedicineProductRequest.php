<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlternativeMedicineProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // يمكنك تعديل الصلاحيات هنا إذا لزم الأمر
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required',
          //  'image' => 'nullable|image',
            'stock' => 'required|integer',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            'name.string' => 'The product name must be a string.',
            'name.max' => 'The product name must not exceed 255 characters.',
            'description.string' => 'The description must be a string.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be in jpeg, png, jpg, or gif format.',
            'image.max' => 'The image size must not exceed 2MB.',
            'stock.required' => 'The stock quantity is required.',
            'stock.integer' => 'The stock quantity must be an integer.',
            'stock.min' => 'The stock quantity cannot be negative.',
        ];
    }
}
