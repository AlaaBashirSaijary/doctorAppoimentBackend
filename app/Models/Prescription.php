<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    // تحديد الجداول التي يتم ملؤها
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'medication_details',
        'instructions',
        'prescription_date',
        'status',
        'prescription_type',
        'doctor_notes',
        'sent_to_patient',
    ];

    // تعريف العلاقة مع موديل المستخدم
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
