<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RDFController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/rdf', [RDFController::class, 'viewData'])->name('rdf.index');
Route::get('/rdf/create', [RDFController::class, 'create'])->name('rdf.create');
Route::post('/rdf/store', [RDFController::class, 'store'])->name('rdf.store');
// 🔹 Tambahan baru
Route::get('/rdf/{name}/edit', [RDFController::class, 'edit'])->name('rdf.edit');
Route::put('/rdf/{name}', [RDFController::class, 'update'])->name('rdf.update');
Route::delete('/rdf/{name}', [RDFController::class, 'destroy'])->name('rdf.destroy');
// 🔹 Akhir tambahan baru
