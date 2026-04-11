<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssociadoController;
use App\Http\Controllers\ReuniaoController;
use App\Models\Associado;
use App\Models\Reuniao;

Route::get('/', function () {
    return redirect('/login');
});

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

    Route::resource('associados', AssociadoController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('associados/{associado}/status', [AssociadoController::class, 'updateStatus'])->name('associados.updateStatus');
    Route::resource('reunioes', ReuniaoController::class)->only(['index', 'create', 'store']);
});
