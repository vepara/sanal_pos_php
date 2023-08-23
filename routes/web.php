<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use \App\Http\Controllers\Payment\PaymentController;

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

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::post('get-pos',[PaymentController::class,'getPos'])->name('get-pos');
Route::get('',[PaymentController::class,'index'])->name('index');
Route::post('payment',[PaymentController::class,'processPayment'])->name('payment');
Route::get('get-token',[PaymentController::class,'getToken'])->name('get-token');
Route::get('success',[PaymentController::class,'success3d'])->name('success');
Route::get('success-page',[PaymentController::class,'successPage3d'])->name('success-page');
Route::get('success-page-2d',[PaymentController::class,'successPage2d'])->name('success-page-2d');
Route::get('error',[PaymentController::class,'error3d'])->name('error');
Route::get('error-page',[PaymentController::class,'errorPage3d'])->name('error-page');
Route::get('error-page-2d',[PaymentController::class,'errorPage2d'])->name('error-page-2d');
Route::get('get-installments',[PaymentController::class,'getInstallment']);
Route::post('send-email',[PaymentController::class,'sendMail'])->name('email-send');
Route::get('check-status',[PaymentController::class,'checkStatus'])->name('check-status');
Route::get('alert',[PaymentController::class,'alert'])->name('alert');

//Route::get('test-mail', function () {
//
//    Mail::to('alperen25inci@gmail.com')->send(new \App\Mail\MyTestMail());
//
//    dd("Email is Sent.");
//});
