<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportPdfController;
use App\Models\Asset;
use App\Models\GatePass;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/device/{asset}/info', function (Asset $asset) {
    return view('assets.info', compact('asset'));
})->middleware('signed')->name('assets.info');

Route::get('/wizy/gate-passes/{gatePass}/print', function (GatePass $gatePass) {
    Gate::authorize('manage gate passes');

    return view('gate-passes.print', [
        'gatePass' => $gatePass->load(['asset.location', 'issuer']),
    ]);
})->middleware('auth')->name('gate-passes.print');

Route::get('/wizy/reports/pdf', ReportPdfController::class)
    ->middleware('auth')
    ->name('reports.pdf');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
