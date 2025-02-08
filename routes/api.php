<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('dishes', DishController::class)->except(['index', 'store', 'delete'])->middleware((['auth:sanctum']));
Route::apiResource('dishes', DishController::class)->only(['store','delete'])->middleware(EnsureUserIsAdmin::class);
Route::apiResource('dishes', DishController::class)->only(['index']);

Route::middleware(['auth:sanctum'])->group(function() {
    Route::controller(CartController::class)->group(function() {
        Route::post('/edit-quantity', 'editQuantity');
        Route::post('/add-to-cart', 'addToCart');
        Route::get('/cart-items', 'getCartItems');
        Route::delete('/remove-cart-item/{id}', 'removeCartItem')->whereNumber('id');
        Route::get('/number-of-cart-items', 'getNumberOfItems');
    });
    
    Route::controller(OrderController::class)->group(function() {
        Route::middleware(['auth:sanctum'])->get('/checkout', 'checkout');
        Route::middleware(['auth:sanctum'])->get('/getCustomerOrders', 'index');
        Route::middleware(['auth:sanctum'])->post('/orders', 'store');
    });
});


Route::group([
    'prefix' => 'admin',
    'middleware' => EnsureUserIsAdmin::class
],
function() {
    Route::get('/login');
    Route::get('/orders', [OrderController::class, 'getAllOrders']);
    Route::post('/modify-order-status/{id}', [OrderController::class, 'modifyOrderStatus'])
        ->whereNumber('id');
});