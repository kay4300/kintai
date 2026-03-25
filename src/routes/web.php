<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Staff\LoginController as StaffLogin;
use App\Http\Controllers\Admin\LoginController as AdminLogin;
use App\Http\Controllers\Staff\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;

// スタッフ
// 画面表示
Route::get('/login', function () {
    return view('staff.login');
})->name('login');
// ログイン処理
Route::post('/login', [StaffLogin::class, 'login']);

// メール認証誘導画面表示
Route::get('/mailenable', function () {
    return view('staff.mailenable');
})->name('mailenable');

// 認証リンク（メール内のURL）
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {

    $user = User::findOrFail($id);

    // メール認証済みにする
    $user->email_verified_at = now();
    $user->save();

    // ログインさせる
    Auth::login($user);

    return redirect('/staff/dashboard');

})->middleware(['signed'])->name('verification.verify');

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

// 管理者画面表示
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');
// ログイン処理
Route::post('/admin/login', [AdminLogin::class, 'login']);

// ログイン後画面表示
Route::get('/staff/dashboard', function () {
    return view('staff.dashboard');
});

//ログアウト 
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');



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
