<?php

 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdvertController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingController;
use App\Models\Review;
use App\Models\Setting;
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
    Route::post('/categories/add',[CategoryController::class,'store']);
    Route::delete('/categories/{id}',[CategoryController::class,'destroy']);
    Route::post('/categories/{id}',[CategoryController::class,'update']);
    Route::get('/categories/{id}',[CategoryController::class,'show'])->name('showCategory');
    Route::get('/categories',[CategoryController::class,'get'])->name('getCategory');
    
    //Adverts
    Route::post('/adverts/add',[AdvertController::class,'store']);
    Route::delete('/adverts/{id}',[AdvertController::class,'destroy']);
    Route::post('/adverts/{id}',[AdvertController::class,'update']);
    Route::get('/adverts/get',[AdvertController::class,'get'])->name('get');
    Route::get('/adverts/{id}',[AdvertController::class,'show'])->name('show');
    Route::get('/adverts',[AdvertController::class,'get_special_for_admin']);
   

    //Sliders
    Route::post('/sliders/add',[SliderController::class,'store']);
    Route::post('/sliders/sort', [SliderController::class, 'reorderSliders']);
    Route::get('/sliders',[SliderController::class,'get']);
    Route::post('/sliders/{id}',[SliderController::class,'update']);
    Route::delete('/sliders/{id}',[SliderController::class,'destroy']);
   
    Route::get('/sliders/{id}',[SliderController::class,'show_slider']);

    //customers
    Route::get('/subscribers',[CustomerController::class,'index']);
    //reviews
    Route::post('/reviews/add',[ReviewController::class,'store']);
    Route::get('/reviews',[ReviewController::class,'get']);
    Route::delete('/reviews/{id}',[ReviewController::class,'destroy']);
    Route::post('/reviews/{id}',[ReviewController::class,'update']);
    Route::get('/reviews/{id}',[ReviewController::class,'show']);
    //partners
    Route::post('/partners/add',[PartnerController::class,'store']);
    Route::get('/partners',[PartnerController::class,'get']);
    Route::delete('/partners/{id}',[PartnerController::class,'destroy']);
    Route::post('/partners/{id}',[PartnerController::class,'update']);
    Route::get('/partners/{id}',[PartnerController::class,'show']);
    //settings 
   // Route::post('/settings/add',[SettingController::class,'store']);
    Route::get('/settings',[SettingController::class,'get']);
   // Route::delete('/settings/{id}',[SettingController::class,'destroy']);
    Route::get('/settings/{data}',[SettingController::class,'show']);
    Route::post('/settings/{id}',[SettingController::class,'update']);
  
    
 });






Route::controller(SliderController::class)->prefix('sliders')->group(function (){
  
    Route::get('/{id}','show_slider');
    Route::post('/sort','sliders_sorting');
   });

Route::controller(CustomerController::class)->prefix('subscribers')->group(function (){
    Route::post('/add','store');
   
    
   });
Route::controller(UserController::class)->prefix('users')->group(function (){
    
    Route::get('/','index')->middleware('api.logger');
    Route::get('/get','get');
    Route::post('/email','sendEmail');
    
    
   });

Route::controller(AdvertController::class)->prefix('adverts')->group(function () {
   
    Route::get('/hidd', 'hidde_main');
    Route::get('/', 'get_data')->name('getAdvert'); // Get adverts by type /category_id
    Route::get('/index', 'index')->name('indexAdvert'); // List all adverts
    Route::get('/suggested', 'suggest')->name('suggestAdvert'); // Get suggested adverts
    
    Route::get('/increase/{id}', 'increase'); // Increase code counter
   // Route::get('/search', 'search')->name('searchAdvert'); // Search adverts by query
    Route::get('/{id}', 'show')->name('showAdvert')->where('id','[0-9]+'); // Get advert by ID
});
   


Route::controller(PartnerController::class)->prefix('partner')->group(function (){
   
    Route::get('/','get');
    
   });
   
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('route:cache');
    return '<h1>View cache cleared</h1>';
});
Route::controller(ReviewController::class)->prefix('reviews')->group(function (){

    Route::get('/','get');
    
   });



   Route::controller(SettingController::class)->prefix('settings')->group(function (){
   
    Route::get('/{key}','show');
});
Route::controller(CategoryController::class)->prefix('categories')->group(function (){

    //return codes and offers belonge to category_id or just codes and offers
    Route::get('/','get');
    Route::get('/{id}','show');
    
});
Route::controller(SettingController::class)->prefix('settings')->group(function (){
 
    Route::get('/','get');
    Route::get('/{id}','show');
    
});
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   