<?php

use App\Http\Controllers\SmsController;
use App\Library\SMS\SmsProvider;
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


Route::prefix("sms")->group(function () {
    Route::put('/providers/{providerName}/activate', [SmsController::class, 'activateProvider']);
    Route::resource('providers', SmsController::class)->except(['create', 'edit', 'destroy']);
    
    Route::post('/update/receiveSms', [SmsController::class, 'updateReceiveSms']);
});


Route::get('mtest', function () {

    $provider = SmsProvider::getProvider();
    dd($provider->getReceivedSms());
    // phpinfo();
});
