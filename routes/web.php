<?php

use App\Http\Controllers\GlucoseController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/rapor', [GlucoseController::class, 'index'])->name('glucose.index');
Route::post('/glucose', [GlucoseController::class, 'store'])->name('glucose.store');
Route::get('/glucose/{id}', [GlucoseController::class, 'show'])->name('glucose.show');
Route::get('/glucose/{id}/edit', [GlucoseController::class, 'edit'])->name('glucose.edit');
Route::put('/glucose/{id}', [GlucoseController::class, 'update'])->name('glucose.update');
Route::delete('/glucose/{id}', [GlucoseController::class, 'destroy'])->name('glucose.destroy');
