<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'patient_condition',
        'is_accepted',
        'requested_at',
        'accepted_at',
        'severity_level',
        'status',
        'specialization'
    ];

    protected $casts = [
        'is_accepted' => 'boolean',
        'requested_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // التحقق مما إذا كان الحجز مفتوحًا أم لا
    public function isAvailable()
    {
        return $this->status === 'pending';
    }
}
