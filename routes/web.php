<?php

use Illuminate\Http\Request;
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

Route::redirect('/', '/login');

Route::get('/login', function (Request $request) {
    if ($request->session()->get('is_logged_in')) {
        return redirect()->route('dashboard');
    }

    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'username' => ['required'],
        'password' => ['required'],
    ]);

    if ($credentials['username'] === 'admin' && $credentials['password'] === 'password') {
        $request->session()->regenerate();
        $request->session()->put('is_logged_in', true);

        return redirect()->route('dashboard');
    }

    return back()->withErrors([
        'username' => 'Invalid credentials.',
    ])->onlyInput('username');
});

Route::get('/dashboard', function (Request $request) {
    if (! $request->session()->get('is_logged_in')) {
        return redirect()->route('login');
    }

    return view('dashboard');
})->name('dashboard');

Route::post('/logout', function (Request $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');
