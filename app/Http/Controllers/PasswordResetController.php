<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class PasswordResetController extends Controller
{
    public function sendResetLink(Request $request)
    {
        // التحقق من صحة البريد الإلكتروني
        $request->validate(['email' => 'required|email']);

        // البحث عن المستخدم
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // إنشاء رمز JWT
        $token = JWTAuth::fromUser($user, ['exp' => now()->addMinutes(30)->timestamp]);

        // إرسال البريد الإلكتروني
        Mail::raw("Here is your password reset token: $token", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Password Reset');
        });

        return response()->json(['message' => 'Reset link sent']);
    }
    public function resetPassword(Request $request)
{
    // التحقق من صحة البيانات
    $request->validate([
        'token' => 'required',
        'password' => 'required|min:6|confirmed',
    ]);

    try {
        // فك تشفير الرمز
        $payload = JWTAuth::setToken($request->token)->getPayload();
        $userId = $payload->get('sub'); // الحصول على معرف المستخدم
        // البحث عن المستخدم
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // تحديث كلمة المرور
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'Password reset successfully']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Invalid or expired token'], 400);
    }

}
}

