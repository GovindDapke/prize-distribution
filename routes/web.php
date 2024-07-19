<?php

use App\Http\Controllers\PrizesController;
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

// Route::get('/', function () {
//     return view('welcome');
// });


// Route::resource('prizes', PrizesController::class);
// Route::post('/prizes/simulate', [PrizesController::class, 'simulate'])->name('prizes.simulate');
// Route::post('/prizes/reset', [PrizesController::class, 'reset'])->name('prizes.reset');


// Route::resource('prizes', PrizesController::class);
// Route::post('/prizes/simulate', [PrizesController::class, 'simulate'])->name('prizes.simulate');
// Route::post('/prizes/reset', [PrizesController::class, 'reset'])->name('prizes.reset');


// Route::resource('prizes', PrizesController::class);


// Route::get('/', function () {
//     return redirect()->route('prizes.index');
// });
// Route::post('/simulate', [PrizesController::class, 'simulate'])->name('simulate');
// Route::post('/reset', [PrizesController::class, 'reset'])->name('reset');
// Route::delete('/delete', [PrizesController::class, 'delete'])->name('delete');


Route::resource('prizes', PrizesController::class);
Route::post('/simulate', [PrizesController::class, 'simulate'])->name('simulate');
Route::post('/reset', [PrizesController::class, 'reset'])->name('reset');

Route::get('/', function () {
    return redirect()->route('prizes.index');
});