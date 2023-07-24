<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\TokenVerificationMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::controller(UserController::class)->group(function (){
    Route::post('/user-registration','UserRegistration');
    Route::post('/user-login','UserLogin');
    Route::post('/send-otp','SendOTP');
    Route::post('/verify-otp','VerifyOTP');
    //Route::post('/reset-password','ResetPassword')->middleware([TokenVerificationMiddleware::class]);
});

Route::middleware('token.verify')->group(function (){
    Route::post('/reset-password',[UserController::class,'ResetPassword']);

    Route::controller(TodoController::class)->group(function (){
        Route::get('/my-todos', 'allTodos');
        Route::get('/my-todo/{id}', 'show');
        Route::post('/create-todo', 'store');
        Route::put('/update-todo/{id}', 'update');
        Route::delete('/delete-todo/{id}', 'destroy');
    });
});
