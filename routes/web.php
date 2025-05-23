<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HistoryController;

// Route utama: redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route untuk Admin
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});

// Route untuk User
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
});

// Route untuk dashboard (dilindungi auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});

// Route untuk data sensor
Route::middleware(['auth'])->group(function () {
    Route::get('/sensor-data', [SensorDataController::class, 'index'])->name('sensor.data');
    Route::get('/sensor-data/latest', [SensorDataController::class, 'getLatestData'])->name('sensor.latest');
});

// Route untuk perangkat
Route::middleware(['auth'])->group(function () {
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
    Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
});

// Route untuk autentikasi
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});

// Route untuk verifikasi email
Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// Route untuk API data sensor terbaru
Route::get('/api/sensor-data/latest', function (Illuminate\Http\Request $request) {
    $deviceId = $request->device_id;
    $sensorId = $request->sensor_id;
    $data = \App\Models\SensorData::where('device_id', $deviceId)
        ->orderByDesc('timestamp')
        ->first();

    // Sesuaikan jika ingin ambil value tertentu
    return response()->json([
        'value_temp' => $data->value_temp ?? null,
        'value_ph' => $data->value_ph ?? null,
        'value_height' => $data->value_height ?? null,
        'timestamp' => $data->timestamp ?? null,
    ]);
});

// Route untuk riwayat
Route::middleware(['auth'])->group(function () {
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/export', [HistoryController::class, 'export'])->name('history.export');
});

// Route untuk API riwayat
Route::get('/api/history', [\App\Http\Controllers\HistoryController::class, 'apiHistory'])->name('history.api');

// Route untuk pengaturan profil
Route::middleware(['auth'])->group(function () {
    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings');
    Route::post('/settings', [ProfileController::class, 'update'])->name('settings.update');
});

// Route untuk profil
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route untuk pengujian MQTT
Route::get('/mqtt-test', [\App\Http\Controllers\MqttTestController::class, 'publish']);

// Route untuk cek koneksi MQTT (AJAX)
Route::get('/devices/check-mqtt', [\App\Http\Controllers\DeviceController::class, 'checkMqttBroker'])->name('devices.check-mqtt');

// Route untuk status perangkat
Route::get('/api/devices/status', function() {
    return \App\Models\Device::select('id', 'status')->get();
});
