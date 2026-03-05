<?php

use App\Services\SeedanceService;
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

Route::get('/home', function (Request $request) {
    if (! $request->session()->get('is_logged_in')) {
        return redirect()->route('login');
    }

    // Get basic stats
    $totalVideos = \App\Models\VideoGeneration::count();
    $processingVideos = \App\Models\VideoGeneration::where('status', 'processing')->count();
    
    return view('home', compact('totalVideos', 'processingVideos'));
})->name('home');

Route::post('/home/prompt', function (Request $request) {
    if (! $request->session()->get('is_logged_in')) {
        return redirect()->route('login');
    }

    $request->validate([
        'prompt' => ['required', 'string'],
    ]);

    return back()->with('status', 'Prompt submitted.');
})->name('home.prompt.submit');

Route::get('/prompts', function (Request $request) {
    if (! $request->session()->get('is_logged_in')) {
        return redirect()->route('login');
    }

    // Fetch only saved prompts, paginated
    $prompts = \App\Models\VideoGeneration::where('is_saved', true)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

    return view('prompts', compact('prompts'));
})->name('prompts');

Route::get('/history', function (Request $request) {
    if (! $request->session()->get('is_logged_in')) {
        return redirect()->route('login');
    }

    // Fetch the video generation history, ordered by newest first, 10 items per page
    $history = \App\Models\VideoGeneration::orderBy('created_at', 'desc')->paginate(10);

    return view('history', compact('history'));
})->name('history');

Route::post('/logout', function (Request $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

