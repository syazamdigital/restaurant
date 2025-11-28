<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');


Route::get('/menu', [OrderController::class, 'showMenu'])->name('customer.menu');
Route::post('/order/submit', [OrderController::class, 'submitCustomerOrder'])->name('customer.submit_order');
Route::get('/kitchen', [OrderController::class, 'showKitchen'])->name('kitchen.index');
Route::post('/order/{id}/status', [OrderController::class, 'updateOrderStatus'])->name('orders.update_status');
Route::get('/order/{id}', [OrderController::class, 'getOrderDetails'])->name('api.order_details'); 
Route::get('/order-counts', [OrderController::class, 'getOrderCounts'])->name('api.order_counts');
Route::get('/api/active-orders', [OrderController::class, 'getActiveOrdersJson'])->name('api.active_orders');
Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update_status');

Route::prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {
    Route::get('/active', 'activeOrders')->name('active');
    Route::get('/history', 'orderHistory')->name('history');
    Route::get('/create', 'create')->name('create');
    Route::get('/{id}', 'show')->name('show');
    Route::post('/{id}/complete', 'completeOrder')->name('complete');
});