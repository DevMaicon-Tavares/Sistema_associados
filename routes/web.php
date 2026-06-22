<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssociadoController;
use App\Http\Controllers\ReuniaoController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Models\Associado;
use App\Models\Reuniao;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/__debug', function () {
    return response()->json([
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
        'app_url' => config('app.url'),
        'app_key_set' => ! empty(env('APP_KEY')),
        'db_connection' => config('database.default'),
        'db_database' => config('database.connections.sqlite.database'),
        'session_driver' => config('session.driver'),
        'log_channel' => config('logging.default'),
        'storage_writable' => is_writable(storage_path()),
    ]);
})->withoutMiddleware([
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    ShareErrorsFromSession::class,
]);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $associadosEmDia = Associado::where('status_pagamento', 'em dia')->count();
        $associadosAtrasados = Associado::where('status_pagamento', 'atrasado')->count();
        $proximasReunioes = Reuniao::where('data', '>=', today())->orderBy('data')->orderBy('horario')->limit(3)->get();

        return view('dashboard', compact('associadosEmDia', 'associadosAtrasados', 'proximasReunioes'));
    })->name('dashboard');

    Route::resource('associados', AssociadoController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::patch('associados/{associado}/status', [AssociadoController::class, 'updateStatus'])->name('associados.updateStatus');
    Route::resource('reunioes', ReuniaoController::class)->parameters([
        'reunioes' => 'reuniao'
    ])->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::get('reunioes/{reuniao}/ata', [ReuniaoController::class, 'downloadAta'])->name('reunioes.ata.download');
    Route::get('reunioes/{reuniao}/ata/view', [ReuniaoController::class, 'viewAta'])->name('reunioes.ata.view');

    Route::get('/api/whatsapp/status', [WhatsAppController::class, 'status'])->name('api.whatsapp.status');
});
