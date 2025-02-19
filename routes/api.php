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
use App\Http\Controllers\AlternativeMedicineProductController;
use App\Http\Controllers\MedicalDocumentController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorRatingController;
use App\Http\Controllers\TranslateController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\EmergencyController;
use App\Http\Controllers\HomeVisitController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\PurchaseController;

Route::get('/products/{productId}/reviews', [ProductReviewController::class, 'index']);
Route::get('/products', [AlternativeMedicineProductController::class, 'index'])->name('products');
Route::get('/products/{id}', [AlternativeMedicineProductController::class, 'show']);
Route::get('/search', [AlternativeMedicineProductController::class, 'search']);
Route::post('/contact', [ContactController::class, 'store']);
Route::post('/password/email', [PasswordResetController::class, 'sendResetLink']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);
Route::get('translate',[TranslateController::class,'getMessage']);
Route::get('doctor/{doctorId}/ratings',[DoctorRatingController::class,'getDoctorRatings']);
Route::get('doctors', [DoctorController::class, 'getAllDoctors']); // لعرض جميع الأطباء
Route::get('doctors/search', [DoctorController::class, 'searchDoctors']); // للبحث عن الأطباء
Route::post('login', [AuthController::class, 'login']);
Route::post('signup-patient', [AuthController::class, 'signupPatient']);
Route::post('signup-doctor', [AuthController::class, 'signupdoctor']);
Route::middleware('auth:api')->post('/update-profile', [AuthController::class, 'updateProfile']);
Route::middleware(['auth:api', 'role:doctor'])->group(function () {
    Route::post('/home-visit/accept/{id}', [HomeVisitController::class, 'acceptHomeVisit']);
    Route::post('/doctor/home-visit-availability', [DoctorController::class, 'updateHomeVisitAvailability']);
    Route::post('/emergency/accept/{id}', [EmergencyController::class, 'acceptEmergency']);
    Route::get('/prescriptions', [PrescriptionController::class, 'index']);
    Route::post('/prescriptions', [PrescriptionController::class, 'store']);
    Route::put('/prescriptions/{id}', [PrescriptionController::class, 'update']);
    Route::delete('/prescriptions/{id}', [PrescriptionController::class, 'destroy']);
    Route::post('/prescriptions/{id}/resend-email', [PrescriptionController::class, 'resendPrescriptionEmail']);
    Route::post('/prescriptions/{id}/cancel', [PrescriptionController::class, 'cancelPrescription']);
    Route::get('/prescriptions/unsent', [PrescriptionController::class, 'showUnsentPrescriptions']);
    Route::delete('/doctor/appointments/{id}', [DoctorController::class, 'cancelAppointmentByDoctor']);
    Route::get('doctor/patients', [DoctorController::class, 'getPatientsForDoctor']);
    Route::post('doctor/create-appointment', [AppointmentController::class, 'createAvailableAppointments']);
    Route::get('doctor/my-appointments', [AppointmentController::class, 'getDoctorAppointments']);
    Route::put('appointments/{appointmentId}/complete', [AppointmentController::class, 'completeAppointment']);
});
Route::middleware(['auth:api', 'role:patient'])->group(function () {
    Route::post('/purchase', [PurchaseController::class, 'purchase']);
    Route::post('/products/{productId}/reviews', [ProductReviewController::class, 'store']);
    Route::get('/medical-documents', [MedicalDocumentController::class, 'getPatientDocuments']);
    Route::post('/medical-documents/{documentId}/share/{doctorId}', [MedicalDocumentController::class, 'shareDocumentWithDoctor']);
    Route::post('/medical-documents/upload', [MedicalDocumentController::class, 'uploadDocument']);
    Route::get('/emergency/doctors', [EmergencyController::class, 'getAvailableDoctors']);
    Route::post('/home-visit/request', [HomeVisitController::class, 'requestHomeVisit']);
    Route::post('/emergency/request', [EmergencyController::class, 'requestEmergency']);
    Route::get('/patient/prescriptions', [PrescriptionController::class, 'patientPrescriptions']);
    Route::post('doctor/{doctorId}/ratings',[DoctorRatingController::class,'addRating']);
    Route::get('patient/appointments', [PatientController::class, 'getPatientAppointments']);
    Route::delete('/patient/appointments/{id}', [PatientController::class, 'cancelAppointment']);
    Route::post('patient/book-appointment/{appointmentId}', [AppointmentController::class, 'bookAppointment']);
});
Route::middleware('auth:api')->get('/prescriptions/search', [PrescriptionController::class, 'searchPrescriptions']);
Route::middleware('auth:api')->get('/prescriptions/{id}', [PrescriptionController::class, 'show']);
Route::middleware('auth:api')->get('/prescriptions/{id}/status', [PrescriptionController::class, 'checkPrescriptionStatus']);
Route::middleware('auth:api')->get('/notifications', [NotificationController::class, 'getNotifications']);
Route::middleware('auth:api')->get('/notifications/unread', [NotificationController::class, 'getUnreadNotifications']);
Route::middleware('auth:api')->post('/notifications/read/{notificationId}', [NotificationController::class, 'markAsRead']);
Route::middleware('auth:api')->get('available-appointments/{doctorId}', [AppointmentController::class, 'getAvailableAppointments']);

Route::middleware('auth:api', 'role:admin')->group(function () {
    Route::post('/admin/products', [AlternativeMedicineProductController::class, 'store']);
    Route::put('/admin/products/{id}', [AlternativeMedicineProductController::class, 'update']);
    Route::delete('/admin/products/{id}', [AlternativeMedicineProductController::class, 'destroy']);
    Route::get('/messages', [ContactController::class, 'getAllMessages']);
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
