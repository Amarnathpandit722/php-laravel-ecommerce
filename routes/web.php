<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CheckoutController;



// Route::get('/', function () {
//     return view('home');
// });


/*
|--------------------------------------------------------------------------
| Guest Routes (Login/Register)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

// Route::middleware('auth')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//     Route::get('/', fn() => redirect('/dashboard'));

//     Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

//     Route::get('/analytics', fn() => view('dashboard'));
//     Route::get('/products', fn() => view('dashboard'));
//     Route::get('/products/create', fn() => view('dashboard'));
//     Route::get('/categories', fn() => view('dashboard'));
//     Route::get('/orders', fn() => view('dashboard'));
//     Route::get('/orders/pending', fn() => view('dashboard'));
//     Route::get('/orders/completed', fn() => view('dashboard'));
//     Route::get('/customers', fn() => view('dashboard'));
//     Route::get('/settings', fn() => view('dashboard'));
//     Route::get('/settings/profile', fn() => view('dashboard'));
//     Route::get('/settings/security', fn() => view('dashboard'));
// });


Route::middleware('auth')->group(function () {
    // ---------- Shared ----------
    Route::get('/messages',                 [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}',  [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages',                [MessageController::class, 'store'])->name('messages.store');

    Route::get('/help',                     [HelpController::class, 'index'])->name('help.index');

    // ---------- Admin + Manager ----------
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/inventory',            [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/reports/sales',        [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/inventory',    [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/customers',    [ReportController::class, 'customers'])->name('reports.customers');
    });
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/reviews',                 [ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    });

    Route::get('/my-reviews', [ReviewController::class, 'mine'])->name('reviews.mine');

    // ---------- Admin only ----------
    Route::middleware('role:admin')->group(function () {
        Route::get('/activity',             [ActivityController::class, 'index'])->name('activity.index');
        Route::get('/team',                 [TeamController::class, 'index'])->name('team.index');
        Route::get('/team/roles',           [TeamController::class, 'roles'])->name('team.roles');
    });

    // ---------- Real-time API ----------
    Route::prefix('api')->group(function () {
        Route::get('/notifications',                    [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read',         [NotificationController::class, 'markRead']);
        Route::post('/notifications/mark-all-read',     [NotificationController::class, 'markAllRead']);
        Route::get('/search',                           [SearchController::class, 'search']);
        Route::get('/activity',                         [ActivityController::class, 'feed']);
    });

    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store');

    Route::put('/reviews/{review}',   [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/helpful', [ReviewController::class, 'markHelpful'])->name('reviews.helpful');


    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', fn() => redirect('/dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    // Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/user-show', [ProductController::class, 'show'])->name('products.user-show');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/pending', [OrderController::class, 'pending'])->name('orders.pending');
    Route::get('/orders/completed', [OrderController::class, 'completed'])->name('orders.completed');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/profile', [SettingsController::class, 'profile'])->name('settings.profile');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::get('/settings/security', [SettingsController::class, 'security'])->name('settings.security');
    Route::put('/settings/security', [SettingsController::class, 'updateSecurity'])->name('settings.security.update');

    /* ---- Cart ---- */
    Route::get('/cart',                [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add',           [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update',       [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove',      [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/count',          [CartController::class, 'count'])->name('cart.count');

    /* ---- Addresses ---- */
    Route::get('/addresses',              [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses',             [AddressController::class, 'store'])->name('addresses.store');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/check',       [AddressController::class, 'checkServiceability'])->name('addresses.check');




    /* ---- Checkout ---- */
    Route::get('/checkout',                     [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/create-order',       [CheckoutController::class, 'createOrder'])->name('checkout.create');
    Route::post('/checkout/verify',             [CheckoutController::class, 'verifyPayment'])->name('checkout.verify');
    Route::get('/checkout/success/{order}',     [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failure',             [CheckoutController::class, 'failure'])->name('checkout.failure');

    /* ---- Orders ---- */
    Route::get('/orders',           [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',   [OrderController::class, 'show'])->name('orders.show');
});
