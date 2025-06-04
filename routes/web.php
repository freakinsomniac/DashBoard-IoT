<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MqttTestController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;

// Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// ==================== AUTHENTICATION ====================
Route::middleware('guest')->group(function () {
    // Register
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Forgot & Reset Password
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// ==================== EMAIL VERIFICATION ====================
Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')->name('verification.send');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ==================== DASHBOARD ====================
Route::middleware('auth')->group(function () {
    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    // User Dashboard
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});

// ==================== SENSOR DATA ====================
Route::middleware('auth')->group(function () {
    Route::get('/sensor-data', [SensorDataController::class, 'index'])->name('sensor.data');
    Route::get('/sensor-data/latest', [SensorDataController::class, 'getLatestData'])->name('sensor.latest');
});

// ==================== DEVICES ====================
Route::middleware('auth')->group(function () {
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
    Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    // MQTT
    Route::get('/devices/check-mqtt', [DeviceController::class, 'checkMqttBroker'])->name('devices.check-mqtt');
});

// ==================== HISTORY ====================
Route::middleware('auth')->group(function () {
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/export', [HistoryController::class, 'export'])->name('history.export');
});

// ==================== PROFILE & SETTINGS ====================
Route::middleware('auth')->group(function () {
    // Settings
    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings');
    Route::post('/settings', [ProfileController::class, 'update'])->name('settings.update');
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Update Password
    Route::post('/user/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});

// ==================== API ROUTES ====================
Route::get('/api/sensor-data/latest', function (Illuminate\Http\Request $request) {
    $deviceId = $request->device_id;
    $data = \App\Models\SensorData::where('device_id', $deviceId)
        ->orderByDesc('timestamp')
        ->first();

    return response()->json([
        'value_temp' => $data->value_temp ?? null,
        'value_ph' => $data->value_ph ?? null,
        'value_height' => $data->value_height ?? null,
        'timestamp' => $data->timestamp ?? null,
    ]);
});
Route::get('/api/history', [HistoryController::class, 'apiHistory'])->name('history.api');
Route::get('/api/devices/status', function() {
    return \App\Models\Device::select('id', 'status')->get();
});

// ==================== MQTT TEST ====================
Route::get('/mqtt-test', [MqttTestController::class, 'publish']);