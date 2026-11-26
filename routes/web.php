<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StaffController;
use App\Http\Middleware\EnsureStaffOrAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Storefront Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'welcome'])->name('welcome');
Route::get('/products', [PageController::class, 'products'])->name('products.catalog');
Route::get('/about', [PageController::class, 'about'])->name('about');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Customers, Staff, Admin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [PageController::class, 'profile'])->name('profile');

    // Cart (customers only - staff/admin redirected away by controller)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
});

/*
|--------------------------------------------------------------------------
| Staff / Admin Protected POS Management Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', EnsureStaffOrAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    // POS Dashboard & Sales
    Route::get('/sale', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sale/fulfill-order', [SaleController::class, 'fulfillOrder'])->name('sales.fulfill');
    Route::get('/history', [SaleController::class, 'history'])->name('sales.history');

    // Staff Register (multi-item session cart)
    Route::post('/sale/register/add',          [SaleController::class, 'registerAdd'])->name('sales.register.add');
    Route::delete('/sale/register/{index}',     [SaleController::class, 'registerRemove'])->name('sales.register.remove');
    Route::delete('/sale/register',             [SaleController::class, 'registerClear'])->name('sales.register.clear');
    Route::post('/sale/register/confirm',       [SaleController::class, 'registerConfirm'])->name('sales.register.confirm');

    // Products Management (Full CRUD)
    Route::resource('products', ProductController::class);

    // Customers Management (Full CRUD)
    Route::resource('customers', CustomerController::class);

    // Staff Management (Full CRUD)
    Route::resource('staff', StaffController::class);
});

require __DIR__.'/auth.php';
