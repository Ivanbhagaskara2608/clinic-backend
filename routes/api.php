<?php

use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// store doctor
Route::post('/doctors', [DoctorController::class, 'store']);

// login
Route::post('/login', [UserController::class, 'login']);
// register
Route::post('/register', [UserController::class, 'store']);

// xendit callback
Route::post('/xendit-callback', [OrderController::class, 'handleCallback']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    // user
    // user/check
    Route::post('/user/check', [UserController::class, 'checkUser']);

    // logout
    Route::post('/logout', [UserController::class, 'logout']);

    //get user
    Route::get('/user/{email}', [UserController::class, 'index']);

    // update google id
    Route::put('/user/{id}', [UserController::class, 'updateGoogleId']);

    // update user
    Route::put('/user/{id}', [UserController::class, 'update']);

    // doctor
    // get all doctors
    Route::get('/doctors', [DoctorController::class, 'index']);

    // update doctor
    Route::put('/doctors/{id}', [DoctorController::class, 'update']);

    // delete doctor
    Route::delete('/doctors/{id}', [DoctorController::class, 'destroy']);

    // get active doctors
    Route::get('/doctors/active', [DoctorController::class, 'getDoctorActive']);

    // get search doctor
    Route::get('/doctors/search', [DoctorController::class, 'searchDoctor']);

    // get doctor by clinic
    Route::get('/doctors/clinic/{clinic_id}', [DoctorController::class, 'getDoctorByClinic']);

    // get doctor by specialist
    Route::get('/doctors/specialist/{specialist_id}', [DoctorController::class, 'getDoctorBySpecialist']);

    // orders
    // store order
    Route::post('/orders', [OrderController::class, 'store']);

    // get all orders
    Route::get('/orders', [OrderController::class, 'index']);

    // get order by patient
    Route::get('/orders/patient/{id}', [OrderController::class, 'getOrderByPatient']);

    // get order by doctor
    Route::get('/orders/doctor/{id}', [OrderController::class, 'getOrderByDoctor']);

    // get order by clinic
    Route::get('/orders/clinic/{id}', [OrderController::class, 'getOrderByClinic']);

    // get olinic summary
    Route::get('/orders/summary/{id}', [OrderController::class, 'getSummary']);
});

