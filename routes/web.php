<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProductController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\NotificationController as CustomerNotificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Webhook\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

// Webhook Routes
Route::post('/webhook/midtrans', [PaymentWebhookController::class, 'handleMidtrans'])->name('webhook.midtrans');

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');

// Universal Dashboard redirect after login
Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    if ($role === 'admin' || $role === 'owner') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('customer.dashboard');
})->middleware(['auth'])->name('dashboard');

// Customer Routes (Authenticated, Verified, Role: customer)
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/promo', [CartController::class, 'applyPromo'])->name('cart.promo.apply');
    Route::delete('/cart/promo', [CartController::class, 'removePromo'])->name('cart.promo.remove');
    
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    
    // Addresses
    Route::get('/customer/addresses', [\App\Http\Controllers\Customer\AddressController::class, 'index'])->name('customer.addresses.index');
    Route::post('/customer/addresses', [\App\Http\Controllers\Customer\AddressController::class, 'store'])->name('customer.addresses.store');
    Route::put('/customer/addresses/{address}', [\App\Http\Controllers\Customer\AddressController::class, 'update'])->name('customer.addresses.update');
    Route::delete('/customer/addresses/{address}', [\App\Http\Controllers\Customer\AddressController::class, 'destroy'])->name('customer.addresses.destroy');
    Route::patch('/customer/addresses/{address}/default', [\App\Http\Controllers\Customer\AddressController::class, 'setDefault'])->name('customer.addresses.set_default');
    
    // Orders
    Route::get('/customer/orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/customer/orders/{id}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::post('/customer/orders/{id}/payment-proof', [CustomerOrderController::class, 'uploadPaymentProof'])->name('customer.orders.upload_proof');
    Route::post('/customer/orders/{id}/cancel', [CustomerOrderController::class, 'cancel'])->name('customer.orders.cancel');

    // Wishlists
    Route::get('/customer/wishlists', [\App\Http\Controllers\Customer\WishlistController::class, 'index'])->name('customer.wishlists.index');
    Route::post('/customer/wishlists/{product}/toggle', [\App\Http\Controllers\Customer\WishlistController::class, 'toggle'])->name('customer.wishlists.toggle');

    // Reviews
    Route::post('/customer/reviews', [\App\Http\Controllers\Customer\ReviewController::class, 'store'])->name('customer.reviews.store');
    Route::put('/customer/reviews/{review}', [\App\Http\Controllers\Customer\ReviewController::class, 'update'])->name('customer.reviews.update');

    // Notifications
    Route::get('/customer/notifications', [CustomerNotificationController::class, 'index'])->name('customer.notifications.index');
    Route::patch('/customer/notifications/{id}/read', [CustomerNotificationController::class, 'markRead'])->name('customer.notifications.mark_read');
    Route::post('/customer/notifications/read-all', [CustomerNotificationController::class, 'markAllRead'])->name('customer.notifications.mark_all_read');
});

// Admin & Owner Routes (Authenticated, Role: admin or owner)
Route::middleware(['auth', 'role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // CRUD Categories & Products
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('products', AdminProductController::class)->except(['show']);
    
    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update_status');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
});

// Profile Routes (Universal for authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
