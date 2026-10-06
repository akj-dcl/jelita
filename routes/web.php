<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/shop', [\App\Http\Controllers\ShopController::class, 'index'])->name('shop');
Route::get('/collections', [\App\Http\Controllers\CollectionController::class, 'index'])->name('collections');
Route::get('/product/{id}', [\App\Http\Controllers\ProductDetailController::class, 'show'])->name('product.detail');
Route::get('/story', function () {
    return \Inertia\Inertia::render('Story');
})->name('story');
Route::get('/about', function () {
    return \Inertia\Inertia::render('About');
})->name('about');
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'store'])->name('cart.add');
Route::put('/cart/{id}', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [\App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout');
    Route::inertia('/checkout-payment', 'CheckoutPayment')->name('checkout.payment');
});

Route::inertia('/order-confirmed', 'OrderConfirmed')->name('order.confirmed');
Route::redirect('/payment-success', '/order-confirmed');
Route::redirect('/bag', '/cart');
Route::middleware(['auth'])->group(function () {
    Route::get('/my-profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('my-profile');
    Route::get('/customer-profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('customer.profile');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::get('/orders/{order}/invoice', [\App\Http\Controllers\ProfileController::class, 'invoice'])->name('orders.invoice');
    Route::post('/orders/{order}/confirm-received', [\App\Http\Controllers\ProfileController::class, 'confirmReceived'])->name('orders.confirm-received');

    // Profile Update (use /my-profile prefix to avoid name collision with Wayfinder)
    Route::post('/my-profile/update-info', [\App\Http\Controllers\ProfileController::class, 'update'])->name('my-profile.update');
    Route::post('/my-profile/change-password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('my-profile.password');

    // Reviews
    Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    // Address CRUD
    Route::post('/addresses', [\App\Http\Controllers\AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [\App\Http\Controllers\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [\App\Http\Controllers\AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/set-default', [\App\Http\Controllers\AddressController::class, 'setDefault'])->name('addresses.setDefault');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');

    Route::get('/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [\App\Http\Controllers\Admin\ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [\App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::delete('/products/{id}/images/{imageId}', [\App\Http\Controllers\Admin\ProductController::class, 'destroyImage'])->name('admin.products.images.destroy');

    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
    Route::delete('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('admin.orders.destroy');
    Route::post('/orders/{order}/verify', [\App\Http\Controllers\Admin\OrderController::class, 'verify'])->name('admin.orders.verify');
    Route::post('/orders/{order}/reject', [\App\Http\Controllers\Admin\OrderController::class, 'reject'])->name('admin.orders.reject');
    Route::post('/orders/{order}/ship', [\App\Http\Controllers\Admin\OrderController::class, 'ship'])->name('admin.orders.ship');
    Route::get('/orders/{order}/invoice', [\App\Http\Controllers\Admin\OrderController::class, 'invoice'])->name('admin.orders.invoice');

    Route::get('/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/create', [\App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/customers/{customer}/edit', [\App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/customers/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/customers/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('admin.customers.destroy');
});
Route::prefix('api')->group(function () {
    Route::get('/destinations', [\App\Http\Controllers\Api\CourierController::class, 'searchDestination']);
    Route::post('/shipping-cost', [\App\Http\Controllers\Api\CourierController::class, 'getShippingCost']);
    Route::post('/checkout/process', [\App\Http\Controllers\CheckoutController::class, 'store']);
});

require __DIR__.'/settings.php';
