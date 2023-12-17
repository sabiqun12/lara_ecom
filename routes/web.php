<?php

use Illuminate\Support\Facades\Route;



use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\TestimonialController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Route::get('/dashboard', function () {
//     return view('backend.pages.dashboard');
// });
// Route::get('/', function () {
//     return view('frontend.pages.home');
// });


Route::prefix('')->group(function(){
    Route::get('/home', [HomeController::class, 'home'])->name('home');
    Route::get('/shop', [HomeController::class, 'shopPage'])->name('shop.page');

});

Route::get('/dashboard',[DashboardController::class,'dashboard'])->name('admin.dashboard');

// Route::get('/home',[HomeController::class,'index'])->name('home');
Route::get('/register', [RegisterController::class, 'registerPage'])->name('register');
Route::post('/register', [RegisterController::class, 'registerStore'])->name('register.store');

Route::get('login', [RegisterController::class, 'loginPage'])->name('login.page');
Route::post('login', [RegisterController::class, 'loginStore'])->name('login.store');
Route::post('logout', [RegisterController::class, 'logout'])->name('logout');


Route::resource('category', CategoryController::class);
Route::resource('testimonial', TestimonialController::class);
Route::resource('product', ProductController::class);



