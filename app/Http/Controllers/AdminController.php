<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // عرض الأطباء غير المصدقين
    public function getPendingDoctors()
    {
        $doctors = User::where('is_verified', false)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'doctor');
            })->get();

        return response()->json(['doctors' => $doctors]);
    }

    // عرض وثيقة الطبيب المحدد
    public function getDoctorCertificate($id)
    {
        $user = User::find($id);

        if (!$user || !$user->hasRole('doctor')) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        if (!$user->certificate_path || !Storage::exists('public/' . $user->certificate_path)) {
            return response()->json(['error' => 'Certificate not found'], 404);
        }

        $certificateUrl = asset('storage/' . $user->certificate_path);

        return response()->json([
            'doctor' => [
                'name' => $user->name,
                'specialization' => $user->specialization,
                'certificate_url' => $certificateUrl
            ]
        ]);
    }


    // تحقق من الطبيب وتفعيل حسابه
    public function verifyDoctor($id)
    {
        $user = User::find($id);

        if (!$user || !$user->hasRole('doctor')) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        $user->is_verified = true;
        $user->save();

        return response()->json(['message' => 'Doctor account verified successfully.']);
    }

    // رفض حساب الطبيب
    public function rejectDoctor($id)
    {
        $user = User::find($id);

        if (!$user || !$user->hasRole('doctor')) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        if ($user->certificate_path && Storage::exists($user->certificate_path)) {
            Storage::delete($user->certificate_path);
        }

        $user->delete();

        return response()->json(['message' => 'Doctor account rejected successfully.']);
    }
}
