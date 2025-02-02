<?php
namespace App\Http\Controllers;

use App\Models\DoctorRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorRatingController extends Controller
{
    public function addRating(Request $request, $doctorId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        $patientId = Auth::id();

        // منع التقييم المكرر
        if (DoctorRating::where('doctor_id', $doctorId)->where('patient_id', $patientId)->exists()) {
            return response()->json(['error' => 'You have already rated this doctor'], 400);
        }

        DoctorRating::create([
            'doctor_id' => $doctorId,
            'patient_id' => $patientId,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json(['message' => 'Rating added successfully']);
    }

    public function getDoctorRatings($doctorId)
    {
        $ratings = DoctorRating::where('doctor_id', $doctorId)->get();

        return response()->json(['ratings' => $ratings]);
    }
}

