<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorRatingController;
Route::get('doctor/{doctorId}/ratings',[DoctorRatingController::class,'getDoctorRatings']);
Route::get('doctors', [DoctorController::class, 'getAllDoctors']); // لعرض جميع الأطباء
Route::get('doctors/search', [DoctorController::class, 'searchDoctors']); // للبحث عن الأطباء
Route::post('login', [AuthController::class, 'login']);
Route::post('signup-patient', [AuthController::class, 'signupPatient']);
Route::post('signup-doctor', [AuthController::class, 'signupdoctor']);
Route::middleware(['auth:api', 'role:doctor'])->group(function () {
    Route::delete('/doctor/appointments/{id}', [DoctorController::class, 'cancelAppointmentByDoctor']);
    Route::get('doctor/patients', [DoctorController::class, 'getPatientsForDoctor']);
    Route::get('doctor/available-appointments/{doctorId}', [AppointmentController::class, 'getAvailableAppointments']);
    Route::post('doctor/create-appointment', [AppointmentController::class, 'createAvailableAppointments']);
    Route::get('doctor/my-appointments', [AppointmentController::class, 'getDoctorAppointments']);
});
Route::middleware(['auth:api', 'role:patient'])->group(function () {
    Route::post('doctor/{doctorId}/ratings',[DoctorRatingController::class,'addRating']);
    Route::get('patient/appointments', [PatientController::class, 'getPatientAppointments']);
    Route::delete('/patient/appointments/{id}', [PatientController::class, 'cancelAppointment']);
    Route::post('patient/book-appointment/{appointmentId}', [AppointmentController::class, 'bookAppointment']);
});
Route::middleware('auth:api')->get('/notifications', [NotificationController::class, 'getNotifications']);
Route::middleware('auth:api', 'role:admin')->group(function () {
    Route::get('admin/pending-doctors', [AdminController::class, 'getPendingDoctors']);
    Route::get('admin/doctor/{id}/certificate', [AdminController::class, 'getDoctorCertificate']);
    Route::post('admin/verify-doctor/{id}', [AdminController::class, 'verifyDoctor']);
    Route::delete('admin/reject-doctor/{id}', [AdminController::class, 'rejectDoctor']);

});
Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

   // Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
