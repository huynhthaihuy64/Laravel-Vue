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
use App\Http\Controllers\Admin\FileHistoryController;
use App\Http\Controllers\Admin\ImportMultipleController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\SendMailAllUserController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\TwoFAController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

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

Route::get('/2fa', [TwoFAController::class, 'index'])->name('2fa.index');
Route::post('/2fa', [TwoFAController::class, 'store'])->name('2fa.post');
Route::get('/2fa/reset', [TwoFAController::class, 'resend'])->name('2fa.resend');

Route::get('/', [MainController::class, 'index'])->name('admin');

Route::get('/redirect/{social}', [SocialAuthController::class, 'redirect'])->name('socialite.redirect');
Route::get('/callback/{social}', [SocialAuthController::class, 'callback'])->name('socialite.callback');

Route::post('/admin/users/login/store', [LoginController::class, 'login'])->name('login');
Route::post('/admin/users/register/store', [LoginController::class, 'register'])->name('register');
Route::group(['middleware' => ['auth:api']], function () {
    Route::prefix('files')->group(function () {
        Route::get('/listFile', [FileHistoryController::class, 'getList'])->name('files.list');
        Route::post('/upload-file', [UploadController::class, 'uploadFileExcel'])->name('files.uploadFile');
        Route::get('/download/{id}', [UploadController::class, 'download'])->name('files.download');
        Route::post('/upload-file-s3', [UploadController::class, 'uploadFileS3'])->name('files.uploadS3');
    });
    Route::post('/sendMailAll', [SendMailAllUserController::class, 'sendAll'])->name('sendMailALl');
    Route::post('/admin/users/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/admin/users/currentUser', [LoginController::class, 'getCurrentUser'])->name('getCurrentUser');
    Route::post('/admin/users/updateCurrentUser', [LoginController::class, 'updateCurrentUser'])->name('updateCurrentUser');
    Route::get('/admin/users/role', [UserRoleController::class, 'index'])->name('roles.list');
    Route::get('/export/{option}', [PageController::class, 'export'])->name('export');
    Route::post('/import', [PageController::class, 'import'])->name('import');
    Route::post('/sendMailContact', [PageController::class, 'sendMailContact'])->name('page.mailContact');
    Route::post('/comment', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/comment/{id}', [CommentController::class, 'commentProduct']);
    Route::prefix('menus')->group(function () {
        Route::post('add', [MenuController::class, 'store'])->name('menus.store');
        Route::get('list', [MenuController::class, 'index'])->name('menus.list');
        Route::get('edit/{id}', [MenuController::class, 'show'])->name('menus.show');
        Route::get('menuProduct/{id}', [MenuController::class, 'getProductsWithMenu'])->name('menus.getProductWithMenu');
        Route::put('edit/{id}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('destroy/{id}', [MenuController::class, 'destroy'])->name('menus.delete');
    });
    // Route::get('documentation');
    Route::prefix('products')->group(function () {
        Route::post('add', [ProductController::class, 'store'])->name('products.store');
        Route::get('list', [ProductController::class, 'index'])->name('products.list');
        Route::get('productMenu', [ProductController::class, 'getProductMenu'])->name('products.productMenu');
        Route::get('relative', [ProductController::class, 'relativeProducts'])->name('products.relativeProduct');
        Route::get('edit/{id}', [ProductController::class, 'show'])->name('products.show');
        Route::put('edit/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('destroy/{id}', [ProductController::class, 'destroy'])->name('products.delete');
        Route::post('search', [SearchProducts::class, 'search'])->name('products.search');
        Route::post('/import-product', [ImportMultipleController::class, 'importMultiple'])->name('products.import');
    });
    Route::prefix('sliders')->group(function () {
        Route::post('add', [SliderController::class, 'store'])->name('sliders.store');
        Route::get('list', [SliderController::class, 'index'])->name('sliders.list');
        Route::get('edit/{id}', [SliderController::class, 'show'])->name('sliders.show');
        Route::put('edit/{id}', [SliderController::class, 'update'])->name('sliders.update');
        Route::delete('destroy/{id}', [SliderController::class, 'destroy'])->name('sliders.delete');
    });
    Route::prefix('users')->group(function () {
        Route::get('list', [LoginController::class, 'listUser'])->name('users.list');
        Route::post('add', [LoginController::class, 'register'])->name('users.store');
        Route::get('edit/{id}', [LoginController::class, 'editUser'])->name('users.show');
        Route::put('edit/{id}', [LoginController::class, 'updateUser'])->name('users.update');
        Route::delete('destroy/{id}', [LoginController::class, 'destroy'])->name('users.delete');
    });
    Route::prefix('charts')->group(function () {
        Route::get('customers/chart', [CustomerController::class, 'chartCustomer'])->name('charts.customer');
        Route::get('customers/chart-revenue', [CustomerController::class, 'chartRevenue'])->name('charts.revenue');
        Route::get('customers/years', [CustomerController::class, 'getYear'])->name('charts.getYear');
    });
    Route::prefix('customers')->group(function () {
        Route::get('list', [CustomerController::class, 'index'])->name('customers.list');
    });
    Route::get('carts', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('add-cart', [App\Http\Controllers\CartController::class, 'addCart'])->name('cart.store');
    Route::get('cart/paypal-success', [App\Http\Controllers\CartController::class, 'success'])->name('cart.success');
    Route::get('cart/{id}', [App\Http\Controllers\CartController::class, 'show'])->name('cart.show');
    Route::post('cart', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::post('cart/payment', [App\Http\Controllers\CartController::class, 'payment'])->name('cart.payment');
    Route::get('cart/remove/{id}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.delete');
    Route::get('list-friend', [ChatController::class, 'index'])->name('chat.listFriend');
    Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/history/{friendId}', [ChatController::class, 'history'])->name('chat.history');
    Route::post('/chat-room/sendChat', [ChatController::class, 'sendChat'])->name('chat.sendMessage');
    Broadcast::channel('chat-room.{user_id}.{friend_id}', function ($user, $userId, $friendId) {
        return $user->id == $friendId;
    });
    Route::get('search-friend', [ChatController::class, 'searchFriend'])->name('chat.searchFriend');
    Route::post('add-friend', [ChatController::class, 'addFriend'])->name('chat.addFriend');
    Route::post('import-multiple', [ImportMultipleController::class, 'importMultiple'])->name('import');
});
Route::post('reset-password', [ResetPasswordController::class, 'sendMail'])->name('common.resetPasswordSendMail');
Route::put('reset-password/{token}', [ResetPasswordController::class, 'reset'])->name('common.resetPassword');

#upload
Route::post('upload/services', [UploadController::class, 'store'])->name('upload');

Route::get('/danh-muc/{id}', [App\Http\Controllers\MenuController::class, 'index'])->name('menus.pageList');
Route::get('/san-pham/{id}', [App\Http\Controllers\ProductController::class, 'index'])->name('products.pageList');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
