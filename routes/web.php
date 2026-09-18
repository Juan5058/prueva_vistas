<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SecurityController;
use App\Livewire\Dashboard;
use App\Livewire\Documents;
use App\Livewire\Proceedings;
use App\Livewire\Reports;
use App\Livewire\Security;
use App\Livewire\Trd;
use App\Livewire\Users;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'trd.session'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/', Dashboard::class)->name('dashboard');

    Route::post('/security/simulate-ip', [SecurityController::class, 'simulateIp'])->name('security.simulate-ip');

    Route::middleware('permission:trd.view')->group(function () {
        Route::get('/trd', Trd\Index::class)->name('trd.index');
        Route::get('/trd/{trdId}', Trd\Show::class)->name('trd.show');
        Route::get('/trd/{trdId}/organo/{dependency}', Trd\Show::class)->name('trd.series');
        Route::get('/trd/{trdId}/organo/{dependency}/serie/{serieId}', Trd\Show::class)->name('trd.holdings');
        Route::get('/trd/{trdId}/organo/{dependency}/serie/{serieId}/expediente/{proceedingId}', Trd\Show::class)->name('trd.expediente');
    });
    Route::middleware('permission:trd.create')->get('/trd-nueva', Trd\Form::class)->name('trd.create');
    Route::middleware('permission:trd.edit')->get('/trd/{trd}/editar', Trd\Form::class)->name('trd.edit');
    Route::middleware('permission:trd.import')->get('/trd-importar', Trd\Import::class)->name('trd.import');

    Route::middleware('permission:proceedings.view')->group(function () {
        Route::get('/expedientes', Proceedings\Index::class)->name('proceedings.index');
        Route::get('/expedientes/{proceedingId}', Proceedings\Show::class)->name('proceedings.show');
    });
    Route::middleware('permission:proceedings.create')->get('/expedientes-nuevo', Proceedings\Form::class)->name('proceedings.create');
    Route::middleware('permission:proceedings.edit')->get('/expedientes/{proceeding}/editar', Proceedings\Form::class)->name('proceedings.edit');

    Route::middleware('permission:documents.view')->group(function () {
        Route::get('/documentos', Documents\Index::class)->name('documents.index');
        Route::get('/documentos/{documentId}', Documents\Show::class)->name('documents.show');
    });
    Route::middleware('permission:documents.download')->get('/documentos/{document}/descargar', [DocumentController::class, 'download'])->name('documents.download');
    Route::middleware('permission:documents.upload')->get('/documentos-nuevo', Documents\Form::class)->name('documents.create');
    Route::middleware('permission:documents.edit')->get('/documentos/{document}/editar', Documents\Form::class)->name('documents.edit');

    Route::middleware('permission:reports.view')->get('/reportes', Reports\Index::class)->name('reports.index');
    Route::middleware('permission:reports.download-pdf')->get('/reportes/inventario.pdf', [ReportController::class, 'inventory'])->name('reports.download-pdf');

    Route::middleware('permission:users.view')->get('/usuarios', Users\Index::class)->name('users.index');
    Route::middleware('permission:users.create')->get('/usuarios-nuevo', Users\Form::class)->name('users.create');
    Route::middleware('permission:users.edit')->get('/usuarios/{user}/editar', Users\Form::class)->name('users.edit');

    Route::middleware('permission:security.view_sessions')->get('/seguridad/sesiones', Security\Sessions::class)->name('security.sessions');
    Route::middleware('permission:security.view_sessions')->get('/seguridad/logs', Security\Logs::class)->name('security.logs');
});
