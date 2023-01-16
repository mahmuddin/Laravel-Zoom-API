<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestingZoomAPIController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/', [TestingZoomAPIController::class, 'index']);
Route::get('callback', [TestingZoomAPIController::class, 'callback']);
Route::get('/get_meetings', [TestingZoomAPIController::class, 'get_meetings']);
Route::get('/create_meeting', [TestingZoomAPIController::class, 'create_meeting']);
Route::get('/delete_meeting/{meetingId}', [TestingZoomAPIController::class, 'delete_meeting']);

// Invite meeting user ajax
Route::get('/autocomplete-ajax-user', [TestingZoomAPIController::class, 'dataAjaxUser']);
Route::post('/add_invitation', [TestingZoomAPIController::class, 'add_invitation']);
Route::post('/view_invitation', [TestingZoomAPIController::class, 'view_invitation']);
Route::delete('/delete_invitation', [TestingZoomAPIController::class, 'delete_invitation']);
Route::post('/verification', [TestingZoomAPIController::class, 'verification']);
