<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\MainController as PageController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\ProductController as SearchProducts;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\Users\LoginController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\TwoFAController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('posts', [PostController::class, 'index']);

Route::get('/2fa', [TwoFAController::class, 'index'])->name('2fa.index');
Route::post('/2fa', [TwoFAController::class, 'store'])->name('2fa.post');
Route::get('/2fa/reset', [TwoFAController::class, 'resend'])->name('2fa.resend');

Route::get('/', [MainController::class, 'index'])->name('admin');
Route::get('/checkAuth', [LoginController::class, 'getAuthenticatedUser']);

Route::get('/redirect/{social}', [SocialAuthController::class, 'redirect']);
Route::get('/callback/{social}', [SocialAuthController::class, 'callback']);

Route::post('/admin/users/login/store', [LoginController::class, 'login']);
Route::post('/admin/users/register/store', [LoginController::class, 'register']);
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/admin/users/logout', [LoginController::class, 'logout']);
    Route::get('/admin/users/currentUser', [LoginController::class, 'getCurrentUser']);
    Route::post('/admin/users/updateCurrentUser', [LoginController::class, 'updateCurrentUser']);
    Route::get('/admin/users/role', [UserRoleController::class, 'index']);
    Route::get('/export/{option}', [PageController::class, 'export']);
    Route::post('/import', [PageController::class, 'import']);
    Route::post('/sendMailContact', [PageController::class, 'sendMailContact'])->name('page.mailContact');
    Route::post('/comment', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/comment/{id}', [CommentController::class, 'commentProduct']);
    Route::prefix('menus')->group(function () {
        Route::post('add', [MenuController::class, 'store']);
        Route::get('list', [MenuController::class, 'index']);
        Route::get('edit/{id}', [MenuController::class, 'show']);
        Route::get('menuProduct/{id}', [MenuController::class, 'getProductsWithMenu']);
        Route::put('edit/{id}', [MenuController::class, 'update']);
        Route::delete('destroy/{id}', [MenuController::class, 'destroy']);
    });
    // Route::get('documentation');
    Route::prefix('products')->group(function () {
        Route::post('add', [ProductController::class, 'store']);
        Route::get('list', [ProductController::class, 'index']);
        Route::get('productMenu', [ProductController::class, 'getProductMenu']);
        Route::get('relative', [ProductController::class, 'relativeProducts']);
        Route::get('edit/{id}', [ProductController::class, 'show']);
        Route::put('edit/{id}', [ProductController::class, 'update']);
        Route::delete('destroy/{id}', [ProductController::class, 'destroy']);
        Route::post('search', [SearchProducts::class, 'search']);
    });
    Route::prefix('sliders')->group(function () {
        Route::post('add', [SliderController::class, 'store']);
        Route::get('list', [SliderController::class, 'index']);
        Route::get('edit/{id}', [SliderController::class, 'show']);
        Route::put('edit/{id}', [SliderController::class, 'update']);
        Route::delete('destroy/{id}', [SliderController::class, 'destroy']);
    });
    Route::prefix('users')->group(function () {
        Route::get('list', [LoginController::class, 'listUser']);
        Route::post('add', [LoginController::class, 'register'])->name('createUser');
        Route::get('edit/{id}', [LoginController::class, 'editUser']);
        Route::put('edit/{id}', [LoginController::class, 'updateUser']);
        Route::delete('destroy/{id}', [LoginController::class, 'destroy']);
    });
    Route::prefix('charts')->group(function () {
        Route::get('customers/chart', [CustomerController::class, 'chartCustomer']);
        Route::get('customers/chart-revenue', [CustomerController::class, 'chartRevenue']);
        Route::get('customers/years', [CustomerController::class, 'getYear']);
    });
    Route::prefix('customers')->group(function () {
        Route::get('list', [CustomerController::class, 'index']);
    });
    Route::get('carts', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('add-cart', [App\Http\Controllers\CartController::class, 'addCart']);
    Route::get('cart/{id}', [App\Http\Controllers\CartController::class, 'show']);
    Route::post('cart', [App\Http\Controllers\CartController::class, 'update']);
    Route::post('cart/payment', [App\Http\Controllers\CartController::class, 'payment']);
    Route::delete('cart/removeAll', [App\Http\Controllers\CartController::class, 'removeAll']);
    Route::get('cart/remove/{id}', [App\Http\Controllers\CartController::class, 'remove']);
});

Route::post('reset-password', [ResetPasswordController::class,'sendMail']);
Route::put('reset-password/{token}', [ResetPasswordController::class,'reset']);

#cart
Route::get('customers', [CustomerController::class, 'index']);
Route::get('customers/view/{id}', [CustomerController::class, 'show']);

#Message
Route::prefix('chat')->group(function () {
    Route::get('/', [ChatsController::class, 'index']);
    Route::get('messages', [ChatsController::class, 'fetchMessages']);
    Route::post('messages', [ChatsController::class, 'sendMessage']);
});
#upload
Route::post('upload/services', [UploadController::class, 'store']);

Route::get('/', [App\Http\Controllers\MainController::class, 'index'])->name('page');
Route::get('/services/load-product/{id}', [App\Http\Controllers\MainController::class, 'loadProduct']);
Route::get('/danh-muc/{id}', [App\Http\Controllers\MenuController::class, 'index']);
Route::get('/san-pham/{id}', [App\Http\Controllers\ProductController::class, 'index']);
Route::get('/about', [PageController::class, 'about'])->name('page.about');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
