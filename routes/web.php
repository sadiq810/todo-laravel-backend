<?php

use App\Http\Controllers\Auth\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(app()->getLocale().'/admin');
});


Route::get('reset-password/{code}', [CustomerController::class, 'loadResetForm'])->name('reset.customer.password');
Route::post('change-customer-password', [CustomerController::class, 'changePassword'])->name('change.customer.password');
