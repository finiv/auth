<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\OpenWeatherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

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

Route::group(['middleware' => ['web']], function () {
    Route::get('/login/google', [AuthController::class, 'redirectToProvider'])->name('login.google');
    Route::get('/login/google/callback', [AuthController::class, 'handleProviderCallback'])->name('login.google.callback');
});

Route::post('/login/email', [AuthController::class, 'loginViaEmail'])->name('login.email');
Route::post('/register/email', [AuthController::class, 'registerViaEmail'])->name('register.email');
Route::get('/', [AuthController::class, 'notAuthResponse'])->name('no_auth_response');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/home', [OpenWeatherController::class, 'index'])->name('home');
});
