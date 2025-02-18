<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\PatientSignUpRequest;
use App\Http\Requests\DoctorSignUpRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','signupPatient','signupdoctor']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     public function login()
     {
         $credentials = request(['email', 'password']);

         // محاولة تسجيل الدخول
         if (!$token = auth()->attempt($credentials)) {
             return response()->json(['error' => 'Email or password doesn\'t exist'], 401);
         }

         $user = auth()->user();

         // التحقق من حالة المستخدم إذا كان دوره دكتور
         if ($user->hasRole('doctor') && !$user->is_verified) {
             return response()->json(['error' => 'Your account is not verified. Please wait for admin approval.'], 403);
         }

         // الحصول على أول دور للمستخدم
         $role = $user->getRoleNames();

         return response()->json([
             'token' => $token,
             'user' => [
                 'name' => $user->name,
                 'email' => $user->email,
                 'role' => $role,
             ],
         ]);
     }
    public function signupPatient(PatientSignUpRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'is_verified' => true,
        ]);
        $user->assignRole('patient');

        return $this->respondWithToken(auth()->login($user));
    }
   public function signupdoctor(Request $req)
    {
   try {

            if (!$req->hasFile('certificate')) {
                return response()->json(['error' => 'Certificate is required'], 422);
            }

            $certificatePath = $req->file('certificate')->store('certificates', 'public');


            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => $req->password,
                'specialization' => $req->specialization,
                'license_number' => $req->license_number,
                'certificate_path' => $certificatePath,
                'is_verified' => false,
            ]);

            $user->assignRole('doctor');

            return response()->json(['message' => 'Registration successful. Awaiting admin approval.'], 201);

        } catch (\Exception $e) {
            Log::error("Signup Error: " . $e->getMessage());
            return response()->json(['error' => 'Registration failed: ' . $e->getMessage()], 500);
        }    }



    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
{
    $user = Auth::user(); // جلب المستخدم المسجّل دخوله

    if (!$user) {
        return response()->json(['error' => 'User not authenticated'], 401);
    }

    // قاعدة التحقق من المدخلات
    $validatedData = $request->validate([
        'name' => 'string|max:255',
        'email' => 'email|unique:users,email,' . $user->id,
        'password' => 'nullable|min:6',
        'phone_number' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // إذا كان المستخدم مريض، السماح بتعديل الأمراض المزمنة
    if ($user->hasRole('patient')) {
        $validatedData['chronic_diseases'] = $request->input('chronic_diseases', $user->chronic_diseases);
    }

    // إذا كان المستخدم طبيب، السماح بتعديل التخصص ورقم الترخيص فقط
    if ($user->hasRole('doctor')) {
        $validatedData['specialization'] = $request->input('specialization', $user->specialization);
        $validatedData['license_number'] = $request->input('license_number', $user->license_number);
    }

    // حفظ الصورة إذا تم رفعها
    if ($request->hasFile('profile_image')) {
        $imagePath = $request->file('profile_image')->store('profile_images', 'public');
        $validatedData['profile_image'] = $imagePath;
    }

    // تحديث بيانات المستخدم
    $user->update($validatedData);

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user
    ]);
}


    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user'=>auth()->user()->name
        ]);
    }
}
