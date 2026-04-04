<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Staff\LoginController as StaffLogin;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Staff\RegisterController;
use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\StampCorrectionRequestController;
use App\Http\Controllers\Staff\RequestController as StaffRequestController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

// スタッフ
// 画面表示
// Route::get('/login', function () {
//     return view('staff.login');
// })->name('login');
// ログイン処理
// Route::post('/login', [StaffLogin::class, 'login']);

// メール認証誘導画面表示
// Route::get('/email/verify', function () {
//     return view('staff.mailenable');
// })->middleware('auth')->name('verification.notice');
Route::get('/mailenable', function () {
    return view('staff.mailenable');
})->middleware('auth')->name('verification.notice');

// 認証リンク（メール内のURL）
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/attendance');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 再送
Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    return back()->with('message', '再送しました');
})->middleware(['auth'])->name('verification.send');

// 登録画面
Route::get('/register', function () {
    return view('staff.register');
})->name('register');
// 登録処理
Route::post('/register', [RegisterController::class, 'register']);

// ログイン後画面表示
// Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance')
// ->middleware(['auth', 'verified']);

//ログアウト 
// Route::post('/logout', function () {
//     Auth::logout();
//     return redirect('/login');
// })->name('logout');
Route::post('/logout', [AttendanceController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    // 勤怠打刻
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('staff.attendance.index');

    Route::post('/attendance/start', [AttendanceController::class, 'startWork'])
        ->name('attendance.start');

    Route::post('/break/start', [AttendanceController::class, 'startBreak'])
        ->name('break.start');

    Route::post('/break/end', [AttendanceController::class, 'endBreak'])
        ->name('break.end');

    Route::post('/attendance/end', [AttendanceController::class, 'endWork'])
        ->name('attendance.end');

    // 勤怠一覧
    Route::get('/attendance/list', [AttendanceController::class, 'list'])
        ->name('staff.attendance.list');

    //勤怠詳細画面
    Route::get('/attendance/{id}/{requestId?}', [AttendanceController::class, 'detail'])
        ->name('staff.attendance.detail');
    // 勤怠修正画面requestIdは申請経由できたものを判別する
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])
        ->name('staff.attendance.update');


    // 申請一覧
    // Route::get('/request/list', [StaffRequestController::class, 'index'])
    //     ->name('staff.request.index');
    // 申請一覧画面表示
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])
        ->name('stamp_correction_request.list');

    // 申請保存
    Route::post('/stamp_correction_request', [StampCorrectionRequestController::class, 'store'])
        ->name('stamp_correction_request.store');   
        
});


Route::prefix('admin')->name('admin.')->group(function () {

    // ログイン画面
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');

    // ログイン処理
    Route::post('/login', [AdminLoginController::class, 'login']);

    //ログアウト 
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    // ログイン後
    Route::middleware('auth:admin')->group(function () {

        // ダッシュボード（勤怠一覧）
        // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        //勤怠詳細画面表示
        Route::get('/attendance/{id}', [DashboardController::class, 'show'])
            ->name('attendance.detail');

        // 勤怠修正
        Route::put('/attendance/{id}', [DashboardController::class, 'update'])
            ->name('attendance.update');
        

        // スタッフ一覧画面
        Route::get('/staff/list', [StaffController::class, 'index'])->name('staff.index');
        // スタッフ別勤怠画面
        Route::get('/staff/{id}/attendance', [StaffController::class, 'attendance'])
            ->name('staff.attendance');
        // csvダウンロード
        Route::get('/staff/{id}/attendance/csv', [StaffController::class, 'csv'])
            ->name('staff.csv');    

        // 申請一覧画面
        Route::get('/stamp_correction_request/list', [AdminRequestController::class, 'index'])->name('request.index');

        
    });
});

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
