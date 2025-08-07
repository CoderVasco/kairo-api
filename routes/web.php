<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\FaqController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


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

Route::post('/scrape-tecnideia', function (Request $request) {
    $apiToken = env('SCRAPE_API_TOKEN');
    
    // Verificação do token diretamente no header
    if ($request->bearerToken() !== $apiToken) {
        return response()->json(['error' => 'Não autorizado'], 401);
    }

    try {
        Artisan::call('scrape:tecnideia');
        $output = Artisan::output();
        Log::info('Comando scrape:tecnideia executado via rota: ' . $output);
        return response()->json(['message' => 'Scraping iniciado com sucesso!', 'output' => $output]);
    } catch (\Exception $e) {
        Log::error('Erro ao executar scrape:tecnideia via rota: ' . $e->getMessage());
        return response()->json(['error' => 'Erro ao iniciar scraping: ' . $e->getMessage()], 500);
    }
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// API Token Management

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/api-token', [ApiTokenController::class, 'index'])->name('api-token');
    Route::post('/api-token/generate', [ApiTokenController::class, 'generateToken'])->name('api-token.generate');
});

//salvar configurações
Route::post('/config/save', [ConfigController::class, 'saveConfig'])->middleware('auth');

// FAQ Management
Route::middleware('auth')->group(function () {
    Route::get('/faqs', [FaqController::class, 'index'])->name('faqs.index');
    Route::get('/faqs/create', [FaqController::class, 'create'])->name('faqs.create');
    Route::post('/faqs', [FaqController::class, 'store'])->name('faqs.store');
    Route::get('/faqs/{faq}/edit', [FaqController::class, 'edit'])->name('faqs.edit');
    Route::put('/faqs/{faq}', [FaqController::class, 'update'])->name('faqs.update');
    Route::delete('/faqs/{faq}', [FaqController::class, 'destroy'])->name('faqs.destroy');
});

require __DIR__ . '/auth.php';
