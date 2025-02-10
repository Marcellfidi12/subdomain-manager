<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubdomainController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/subdomains', [SubdomainController::class, 'index'])->name('subdomain.index');
Route::post('/subdomains', [SubdomainController::class, 'store'])->name('subdomain.store');
Route::get('/subdomains/{recordId}/edit', [SubdomainController::class, 'edit'])->name('subdomain.edit');
Route::put('/subdomains/{recordId}', [SubdomainController::class, 'update'])->name('subdomain.update');
Route::delete('/subdomains/{recordId}', [SubdomainController::class, 'destroy'])->name('subdomain.destroy');






