<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'document_type', 'file_path', 'description'
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
