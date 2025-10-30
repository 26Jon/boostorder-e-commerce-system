<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

//Auth Routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
});

//User Routes
Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'getAllUsers');
    Route::get('/user/{id}', 'getUserById');
});

//Products Routes
Route::controller(ProductsController::class)->group(function () {
    Route::get('/products', 'getAllProducts');
    Route::get('/product/{id}', 'getProductById');
});

//Cart Routes
Route::controller(CartController::class)->group(function () {
    Route::get('/cart/user/{userId}','getCartByUserId');
    Route::post('/cart/add','addToCart');
    Route::post('/cart/checkout','checkoutCart');
    Route::put('/user/{userId}/cart/{cartItemId}','updateQuantity');
    Route::delete('/user/{userId}/cart/{cartItemId}','removeCartItem');
});

//Order Routes
Route::controller(OrderController::class)->group(function () {
    Route::get('/order/user/{userId}','getOrdersByUserId');
    Route::put('/order/{orderId}/status','updateOrderStatus');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
