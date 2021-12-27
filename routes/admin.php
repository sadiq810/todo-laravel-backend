<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\{LanguageController, DashboardController, CategoryController, PagesController,
    FaqController, UserController, BlogController, RoleController, FeatureController, OptionController,
    ItemController, OrderController, ContactusController, SocialController, SupportController, CustomerController,
    MessageController, ComplaintController, StatsController
};


Route::get('/admin', function () {
    return redirect(app()->getLocale().'/admin');
});

Route::group(['prefix' => '{locale}', 'where' => ['locale' => '[a-zA-Z]{2}'], 'middleware' => ['setlocale']], function () {
    Route::group(['prefix' => 'admin'], function () {
        Auth::routes();

        Route::group(['middleware' => 'auth',], function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

            Route::get('users', [UserController::class, 'index'])->name('users.index');
            Route::get('users-list', [UserController::class, 'loadUsersForDataTable'])->name('users.list');
            Route::post('users-save', [UserController::class, 'save'])->name('users.save');
            Route::post('update-user-field', [UserController::class, 'updateField'])->name('update.user.field');
            Route::delete('delete-user/{id}', [UserController::class, 'delete'])->name('users.destroy');
            Route::get('change-password', [UserController::class, 'loadChangePasswordView'])->name('change.password');
            Route::post('change-password', [UserController::class, 'changePassword'])->name('change.password');


            Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('customers-list', [CustomerController::class, 'loadUsersForDataTable'])->name('customers.list');
            Route::get('customers-checkins-list', [CustomerController::class, 'loadCheckinsForDataTable'])->name('customer.checkins.grid');
            Route::post('customers-save', [CustomerController::class, 'save'])->name('customers.save');
            Route::post('update-customer-field', [CustomerController::class, 'updateField'])->name('update.customers.field');
            Route::delete('delete-customer/{id}', [CustomerController::class, 'delete'])->name('customers.destroy');
    });
    });
});
