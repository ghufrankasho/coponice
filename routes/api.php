<?php

 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdvertController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ReviewController;
use App\Models\Review;
use Illuminate\Http\Request;
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

// Route::controller(CategoryController::class)->prefix('category')->group(function (){
  
//     //return codes or offers belonge to category_id or just codes or offers
//     Route::get('/adverts','get_data'); 
   
    
//     //return codes and offers belonge to category_id or just codes and offers
//     Route::get('/show/{id?}','show_data');
    
//    });
   
   
 

Route::group(['middleware'=>'api','prefix'=>'auth'],function($router){
    
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/profile',[AuthController::class,'profile']);
    Route::post('/refresh',[AuthController::class,'refresh']);
    Route::post('/reset', [AuthController::class,'reset_password']);
 });
 

Route::group(['middleware'=>'auth:api','prefix'=>'admin'],function($router){
    
    //Categories
    Route::post('/category/add',[CategoryController::class,'store']);
    Route::delete('/category/{id}',[CategoryController::class,'destroy']);
    Route::post('/category/{id}',[CategoryController::class,'update']);
    Route::get('/category/{id}',[CategoryController::class,'show'])->name('showCategory');
    Route::get('/category',[CategoryController::class,'get'])->name('getCategory');
    
    //Adverts
    Route::post('/advert/add',[AdvertController::class,'store']);
    Route::delete('/advert/{id}',[AdvertController::class,'destroy']);
    Route::post('/advert/{id}',[AdvertController::class,'update']);
    Route::get('/advert/get',[AdvertController::class,'get'])->name('get');
    Route::get('/advert/{id}',[AdvertController::class,'show'])->name('show');
    Route::get('/advert',[AdvertController::class,'get_special_for_admin']);
   

    //Sliders
    Route::post('/slider/add',[SliderController::class,'store']);
    Route::get('/slider',[SliderController::class,'get']);
    Route::post('/slider/{id}',[SliderController::class,'update']);
    Route::delete('/slider/{id}',[SliderController::class,'destroy']);
    Route::get('/slider/hide',[SliderController::class,'hide']);
    Route::get('/slider/{id}',[SliderController::class,'show']);

    //customers
    Route::get('/customer',[CustomerController::class,'index']);
    //reviews
    Route::post('/review/add',[ReviewController::class,'store']);
    Route::delete('/review/{id}',[ReviewController::class,'destroy']);
    Route::post('/review/{id}',[ReviewController::class,'update']);
    Route::get('/review/{id}',[ReviewController::class,'show']);
 });

Route::controller(SliderController::class)->prefix('slider')->group(function (){
  
    Route::get('/{id}','show_slider');
   });

Route::controller(CustomerController::class)->prefix('subscription')->group(function (){
    Route::post('/add','store');
   
    
   });
Route::controller(UserController::class)->prefix('user')->group(function (){
    
    Route::get('/','index')->middleware('api.logger');
    Route::get('/get','get');
    Route::post('/email','sendEmail');
    
    
   });

Route::controller(AdvertController::class)->prefix('advert')->group(function () {
   
    Route::get('/', 'get_data')->name('getAdvert'); // Get adverts by type /category_id
    Route::get('/index', 'index')->name('indexAdvert'); // List all adverts
    Route::get('/suggested', 'suggest')->name('suggestAdvert'); // Get suggested adverts
    
    Route::get('/increase/{id}', 'increase'); // Increase code counter
    Route::get('/{query}', 'search')->name('searchAdvert')->where('query', '[A-Za-z]+'); // Search adverts by query
    Route::get('/{id}', 'show')->name('showAdvert')->where('id','[0-9]+'); // Get advert by ID
});
   


   Route::controller(PartnerController::class)->prefix('partner')->group(function (){
   
    Route::post('/add','store')->middleware('auth:api');
    Route::get('/','index');
    Route::delete('/{id}','destroy')->middleware('auth:api');
    Route::post('/{id}','update')->middleware('auth:api');
    
   });
   

Route::controller(ReviewController::class)->prefix('review')->group(function (){
   
   
    Route::get('/','index');
    
   });
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   