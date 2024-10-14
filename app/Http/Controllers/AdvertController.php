<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\Category;
use App\Models\Partner;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Slider;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Query\Builder;

class AdvertController extends Controller
{
    public function index(){
        $category=Category::get();
    
        $offers= Advert::where([['type',0],['visible',1]])->orderBy('main', 'desc')->orderBy('updated_at', 'desc')->take(12)->get()->toArray();
        $codes= Advert::where([['type',1],['visible',1]])->orderBy('main', 'desc')->orderBy('updated_at', 'desc')->take(12)->get()->toArray();
        $specials= Advert::where([['type',null],['visible',1]])->orderBy('main', 'desc')->orderBy('updated_at', 'desc')->take(12)->get()->toArray();
       
       
        $slider1=Slider::where([['type',1],['visible',1]])->orderby('sorting','DESC')->get();
        $slider2=Slider::where([['type',2],['visible',1]])->orderby('sorting','DESC')->get();
        $slider3=Slider::where([['type',3],['visible',1]])->orderby('sorting','DESC')->get();
        $slider4=Slider::where([['type',4],['visible',1]])->orderby('sorting','DESC')->get();
        $slider5=Slider::where([['type',5],['visible',1]])->orderby('sorting','DESC')->get();
        $special_name=Setting::where('key','specialOffersName')->first();
        $reviews=$this->get_reviews();
        $partners=$this->get_partners();
  
          return response()->json([
                
                'categories'=>$category,
                'offers'=>$offers,
                'codes'=>$codes,
                'specials'=>$specials,
                'slider1'=>$slider1,
                'slider2'=>$slider2,
                'slider3'=>$slider3,
                'slider4'=>$slider4,
                'slider5'=>$slider5,
                'specialOfferName'=>$special_name,
                'reviews'=>$reviews,
                'partner'=>$partners,
                 ], 200);
        
    }
    private function get_reviews(){
        try{ 
                $Review=Review::where('visible',1)->latest()->get();
                
              
                return $Review;
                
            }
        catch (ValidationException $e) {
                return response()->json(['errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                return response()->json(['message' => 'An error occurred while getting  the data.'], 500);
            }    
      
        
    }
    private function get_partners(){
        try{ 
                $partner=partner::where('visible',1)->latest()->get();
                
                return $partner ;
            }
        catch (ValidationException $e) {
                return response()->json(['errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                return response()->json(['message' => 'An error occurred while getting  the data.'], 500);
            }    
      
        
    }
    public function get(){
        try{
            $adverts=advert::latest()->get();
            
            if($adverts){
                return response()->json(
                $adverts
                    
                 , 200);
               }
           else  return response()->json(
               null
                    
                 , 422);
          
           }
            
            
        
        catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while requesting  adverts .'], 500);
          }
        
    }
    public function show($id){
       
        try {  
            
            $input = [ 'id' =>$id ];
            $validate = Validator::make( $input,
                ['id'=>'required|integer|exists:adverts,id']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
                 'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 422);}
          
            $advert=advert::find($id);
            
            return response()->json(
             $advert
                  
               , 200);
            }
   
        catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while requesting this advert .'], 500);
          }
    }
    public function get_data(Request $request){
        try {  
           
      
            $validate = Validator::make( $request->all(),
                [
                'category_id'=>'integer|exists:categories,id',
                
            ]);
            if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validate->errors()
                ], 422);
            }
            $type=null;
            $page=1;
            $limit=12;
            if($request->has('search')){
                
                return $this->search($request);
            }
            if($request->filled('page')){
                 
                  $page=$request->page;
                 }
            if($request->filled('limit')){
                  $limit=$request->limit;
                  
                 }
            if($request->filled('type')){
                    $type=$request->type;
                   }
            if(!$request->filled('category_id'))
            {
            return $this->get_adverts($page,$limit,$type);  
            }
            else{
                return $this->get_adverts($page,$limit,$type,$request->category_id);  
            } 
          
            
                
         }
        catch (ValidationException $e) {
              return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
              return response()->json(['message' => $e,
              'An error occurred while abtaining the adverts.'
            ], 500);
          }
    }
    private function get_adverts($page,$limit,$type,$category_id=null){
        
        
       if($page <=1){
            $value=0;
            
        }
        else{
            $value=($page-1)*$limit;
            }
       
        if($category_id==null)    
        {     
            $advert= Advert::where([['type',$type],['visible',1]])->offset($value)
                ->limit($limit)->orderBy('main', 'desc')->orderBy('updated_at', 'desc')
                ->get();
            $number= count(Advert::where([['type',$type],['visible',1]])
                ->orderBy('main', 'desc')->orderBy('updated_at', 'desc')
                ->get());
        }
        else{
            $advert= Advert::where([['type',$type],['visible',1],['category_id',$category_id]])->offset($value)
                ->limit($limit)->orderBy('main', 'desc')->orderBy('updated_at', 'desc')
                ->get(); 
            $number= count(Advert::where([['type',$type],['visible',1],['category_id',$category_id]])
                ->orderBy('main', 'desc')->orderBy('updated_at', 'desc')
                ->get());
        }
                
                    
       
       
          return response()->json(
                ['result'=>$advert,
                'total'=>$number]
                 , 200);
    }
    public function get_special_for_admin(Request $request){
        // return $advert= Advert::where('type',null)->get();
     
        $page=1;
        $limit=12;
       
          if(!$request->filled('page')){
              $page=$request->page;
             }
          if($request->filled('limit')){
              $limit=$request->limit;
              
             }
            
       if($page <=1){ $value=0;}
        else{
                $value=($page-1)*$limit;
            }
        $advert= Advert::where('type',null)->offset($value)
        ->limit($limit)->orderBy('main', 'desc')->orderBy('updated_at', 'desc')->get();
   
         
         
            
     return response()->json($advert, 200);
    }
    public function store(Request $request){
        
        try{
            
            if($request->type==1){
                
                 $validateadvert = Validator::make($request->all(), 
            [
                'name' => 'string|required',
                'link' => 'nullable|url:http,https',
                'description' => 'nullable|string',
                 
                'discount' => 'integer|between:0,100|required',
                'code' => 'string|nullable|required',
                'type' => 'bool',
                'main' => 'bool|required',
                'category_id' => 'integer|exists:categories,id',
                'image' => 'file|required|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                //new columns
                'visible' => 'bool',
                'expire_date' => 'nullable|date',
                'short_description' => 'nullable|string',
                // seo columns
                'seo_image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                'seo_title' => 'nullable|string',
                'seo_description' => 'nullable|string',
                'seo_keywords' => 'nullable|string',
            ]);
            $validateadvert->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                  return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
            $validateadvert->sometimes('seo_image', 'mimetypes:image/vnd.wap.wbmp', function ($input) {
                return $input->file('seo_image') !== null && $input->file('seo_image')->getClientOriginalExtension() === 'wbmp';
          });
            }
            else{
                 
                 $validateadvert = Validator::make($request->all(), 
            [
                'name' => 'string|required',
                'link' => 'url:http,https|required',
                'description' => 'string|required',
                'discount' => 'integer|between:0,100',
                'code' => 'string|nullable',
                'type' => 'bool',
                'main' => 'bool|required',
                'category_id' => 'integer|exists:categories,id',
                'image' => 'file|required|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                //new columns
                'visible' => 'bool',
                'expire_date' => 'nullable|date',
                'short_description' => 'nullable|string',
                // seo columns
                'seo_image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                'seo_title' => 'nullable|string',
                'seo_description' => 'nullable|string',
                'seo_keywords' => 'nullable|string',
            ]);
            $validateadvert->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                  return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
            $validateadvert->sometimes('seo_image', 'mimetypes:image/vnd.wap.wbmp', function ($input) {
                return $input->file('seo_image') !== null && $input->file('seo_image')->getClientOriginalExtension() === 'wbmp';
          });
            }
           
            if($validateadvert->fails()){
                return response()->json([
                    'status' => false,
                     'message' => 'خطأ في التحقق',
                    'errors' => $validateadvert->errors()
                ], 422);
            }
                      
 

            $advert = Advert::create(array_merge(
                $validateadvert->validated()
                 ));
            if($request->category_id != null){

                $category=Category::find($request->category_id);
                $advert->category()->associate($category);
            }
                
            if($request->hasFile('image') and $request->file('image')->isValid()){
                $advert->image = $this->storeImage($request->file('image'),'adverts'); 
            }
            if($request->hasFile('seo_image') and $request->file('seo_image')->isValid()){
                $advert->seo_image = $this->storeImage($request->file('seo_image'),'adverts'); 
            }
            
            $result=$advert->save();
           if ($result){
                $adverts=Advert::where('type',$advert->type)->latest()->get();
                return response()->json(
                 $adverts
                 , 200);
          }
            else{
                return response()->json(null, 422);
            }

        }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
       
        
    }
    public function destroy($id){
        try {  
             
            $input = [ 'id' =>$id ];
            $validate = Validator::make( $input,
                ['id'=>'required|integer|exists:adverts,id']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
               'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 422);}
          
            $advert=advert::find($id);
           
            if($advert)
            { 
                if($advert->image!=null) 
                {
                      $this->deleteImage($advert->image);
                }
             
                if($advert->seo_image!=null) 
                {
                    $this->deleteImage($advert->seo_image);
                }
               
                $type=$advert->type;
                $result= $advert->delete();
               if($result) {
                $adverts=Advert::where('type',$type)->latest()->get();
                return response()->json(
                   $adverts
                 , 200);
                }
            }
    
           
                  
              
              
                return response()->json(null, 422);
          }
          catch (ValidationException $e) {
              return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
              return response()->json(['message' => 
              'An error occurred while deleting the advert.'
            ], 500);
          }
    }
    public function update(Request $request, $id){
        try{
            $input = [ 'id' =>$id ];
            $validate = Validator::make( $input,
            ['id'=>'required|integer|exists:adverts,id']);
            if($validate->fails()){
                    return response()->json([
                        'status' => false,
                         'message' => 'خطأ في التحقق',
                        'errors' => $validate->errors()
                    ], 422);
                }
                
            $advert=Advert::find($id);
            
            if($advert->type==1){
               $validateadvert = Validator::make($request->all(), 
                [
                    'name' => 'string',
                    'link' => 'url:http,https|nullable',
                   
                    'description' => 'string|nullable',
                    'type'=>'bool|nullable',
                    'discount' => 'integer|between:0,100',
                    'code'=>'string',
                    'main' => 'bool|nullable',
                    'category_id' => 'integer|nullable|exists:categories,id',
                    'image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                    //new columns
                    'visible' => 'bool',
                    'expire_date' => 'nullable|date',
                    'short_description' => 'nullable|string',
                    // seo columns
                    'seo_image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                    'seo_title' => 'nullable|string',
                    'seo_description' => 'nullable|string',
                    'seo_keywords' => 'nullable|string',

            ]);
            $validateadvert->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                  return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
            $validateadvert->sometimes('seo_image', 'mimetypes:image/vnd.wap.wbmp', function ($input) {
                return $input->file('seo_image') !== null && $input->file('seo_image')->getClientOriginalExtension() === 'wbmp';
          });  
            }
            else{
                 $validateadvert = Validator::make($request->all(), 
                [
                    'name' => 'string',
                    'link' => 'url:http,https',
                   
                    'description' => 'string',
                    'type'=>'bool|nullable',
                    'discount' => 'integer|nullable|between:0,100',
                    'code'=>'string|nullable',
                    'main' => 'bool',
                    'category_id' => 'integer|exists:categories,id',
                    'image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                    //new columns
                    'visible' => 'bool',
                    'expire_date' => 'nullable|date',
                    'short_description' => 'nullable|string',
                    
                    // seo columns
                     
                    'seo_image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                    'seo_title' => 'nullable|string',
                    'seo_description' => 'nullable|string',
                    'seo_keywords' => 'nullable|string',

        ]);
        $validateadvert->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
        return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
        });
        $validateadvert->sometimes('seo_image', 'mimetypes:image/vnd.wap.wbmp', function ($input) {
        return $input->file('seo_image') !== null && $input->file('seo_image')->getClientOriginalExtension() === 'wbmp';
        }); 
            
            }
               
               

            if($validateadvert->fails()){
                return response()->json([
                    'status' => false,
                     'message' => 'خطأ في التحقق',
                    'errors' => $validateadvert->errors()
                ], 422);
            }
            $advert = advert::find($id);
           
 
            if($advert)  
               {  
                $advert->update($validateadvert->validated());
                if($request->hasFile('image') and $request->file('image')->isValid()){
                    if($advert->image !=null){
                        $this->deleteImage($advert->image);
                    }
                    $advert->image = $this->storeImage($request->file('image'),'adverts'); 
                }
                if($request->hasFile('seo_image') and $request->file('seo_image')->isValid()){
                    if($advert->seo_image !=null){
                        $this->deleteImage($advert->seo_image);
                    }
                    $advert->seo_image = $this->storeImage($request->file('seo_image'),'adverts'); 
                }
                if($request->category_id != null){

                    $category=Category::find($request->category_id);
                    $advert->category()->associate($category);
                }
                
                $advert->save();
                $adverts=Advert::where('type',$advert->type)->latest()->get();
                return response()->json(
                 $adverts
                 , 200);
            }
            else{
                return response()->json([
                    'status' => false,
                    'message' =>  'فشلت عملية التعديل ',
                    'data'=> null
                     ], 422);
            }

        }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
      
        
    }
    public function suggest(Request $request){
        
       $type=0;
       //    $page=1;
       $limit=12;
        # this function suggest newly added adverts that belongto most commen categories
        # commen category is the category its advert has higher counter
        if($request->filled('type')){
            $type=$request->type;
           }
        // if(!$request->filled('page')){
        //     $page=$request->page;
        //    }
        if($request->filled('limit')){
            $limit=$request->limit;
            
           }
            
        if($type==1)
        {
             #setp 1 ::get adverts with highest counter
            $highest_offers=Advert::where([['type',1],['visible',1]])->orderBy('counter', 'desc')->take(10)->get();
            $cat_offers=array();
            
            foreach($highest_offers as $offer){ 
                
                #step 2::get adverts that belongs to most commen category
                $offers=Advert::where([['category_id',$offer->category->id],['type',1],['visible',1]])->orderBy('discount', 'desc')->latest()->take(10)->get();
                
                array_push($cat_offers , $offers); 
            }
        
            $final_offer=array();
            foreach($cat_offers as $arr_offer){
                foreach($arr_offer as $offer){
                    if(!in_array($offer,$final_offer) and $offer->visible and count($final_offer)<$limit)
                    {
                        array_push($final_offer,$offer);
                    }
                }
            }
            return response()->json(
            
                $final_offer
                , 200);
            }
        else{
            $input = [ 'id' =>$request->id ];
            $validate = Validator::make( $input,
                ['id'=>'required|integer|exists:adverts,id']);
                
            if($validate->fails()){
                return response()->json([
                    'status' => false,
                'message' => 'خطأ في التحقق',
                    'errors' => $validate->errors()
                ], 422);
            }
            $advert=Advert::find($request->id );
            $adverts=Advert::where([['category_id',$advert->category_id],['type',0],['visible',1]])->latest()->take($limit+1)->get()->toArray();
            
             
            $ads=array();
            foreach($adverts as $of)
            {
                if ($request->id !=$of['id'] and count($ads)<$limit) {
                    array_push($ads, $of);
                 }
             }
            if(count($ads)<3)
            { 
                $offers= Advert::where('type',0)->inRandomOrder()->take($limit)->get()->toArray(); 
                 
                 
                foreach($offers as $of)
                {
                    if ($request->id !=$of['id'] and ! in_array($of,$ads) and count($ads)<$limit) {
                    array_push($ads, $of);
                }
                    
                }
               
           }
              return response()->json(
           
             $ads
             , 200);
            
            
            
            
            
        }
       
         
    }
    public function increase($id){
        try {  
             
            $input = [ 'id' =>$id ];
            $validate = Validator::make( $input,
                ['id'=>'required|integer|exists:adverts,id']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
                 'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 422);}
          
            $advert=advert::find($id);
             
            if($advert)
            { 
                $advert->counter +=1;
                $advert->save();
                return response()->json([
                
                'data'=> [$advert]
                  
               ], 200);
            }
                return response()->json([
                  'status' => true,
                  'message' =>  'something went wrong ',
                  'data'=> null
                 
              ], 422);
          }
          catch (ValidationException $e) {
              return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
              return response()->json(['message' => 'An error occurred while increasing the counter of coped advert.'], 500);
          }  
    }
    private function search(Request $request){
        try {
        
            $type=0;
            $page=1;
            $limit=12;
            $value=0;
            if($request->filled('type')){
                  $type=$request->type;
                }
            if($request->filled('page')){
               
                  $page=$request->page;
                }
            if($request->filled('limit')){
                  $limit=$request->limit;
                  
                }
            if($request->filled('search')){
                    $search=$request->search;
                    
                }
            if($page >1){
                 $value=($page-1)*$limit;
                    
                }
                 
            $validatesearch = Validator::make($request->all(), 
                [ 'search' => 'required|string|min:3' ]); 
                
            if($validatesearch->fails())
            {
               
                $adverts=advert::where('visible',1)->offset($value)
                ->limit($limit)->orderBy('main', 'desc')->orderBy('updated_at', 'desc')->get();
                $number=count(advert::where('visible',1)->get());
                if($adverts){
                    return response()->json(
                    [
                        'result'=>$adverts,
                        'total'=>$number]
                        
                     , 200);
                   }
               else  return response()->json(
                   null
                        
                     , 422);  
            }
            
             
                 
            $adverts=array();
             
            $category= Category::where('name','LIKE', '%' . $search .'%')->with('advert')->first();
            
            if($category)
            { 
                $adverts= $category['advert'];
                
            }
           
            $data = Advert::where('name','LIKE', '%' . $search .'%')
                ->orwhere('description','LIKE', '%' . $search .'%')->get();      
              
            
            if(count($data)>0)
            {
                
                if(count($adverts)>0){
                    
                    $data=$adverts->merge($data);
                }
                $result=array();
                $count=0;
                foreach($data as $advert){
                    
                    if(! in_array($advert,$result)  and $advert->type==$type and $advert->visible)
                    { 
                        $count+=1;
                        array_push($result , $advert);
                        
                    }
                }
        
                $slicedData = array_slice($result, $value, $limit);
                
                if ($slicedData)
                { 
                    return response()->json(
                            
                        ['result'=>$slicedData,
                        'total'=>$count]
                        , 200);
                }
                else{
                    return response()->json( ['result'=>[],
                    'total'=>0],200); 
                    
                }
            }
            else
            {
      
                if(count($adverts)>0)
               { 
                    $result=array();
                    $count=0;
                    foreach($adverts as $advert){
                        if(! in_array($advert,$result)  and $advert->type==$type  and $advert->visible){
                            $count+=1;
                            array_push($result , $advert);
                        }
                    }
                    
                    $slicedData = array_slice($result, $value, $limit);
                    
                    if ($slicedData)
                    { return response()->json(
                                
                        ['result'=> $slicedData,
                        'total'=>$count
                        ]
                        , 200);  }
            }
            else{
                return response()->json( ['result'=>[],
                'total'=>0],200); 
            }
           
            }
            
            
             
                
           
            
                    
                    
                     
                  }
        catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } 
        catch (\Exception $e) {
            return response()->json(['message' =>$e,
             'An error  occurred while requesting this Product.'], 500);
        }

    }
    public function specifiy_search($inputLine){
        $result = null;
        $wordArray = explode(' ', $inputLine);
        $wordArray = array_filter($wordArray);
        $offerWords = ['عرض', 'عروض', 'offer', 'offers'];
        $codeWords = ['code', 'codes', 'اكواد', 'أكواد', 'إكواد', 'كود', 'خصم', 'خصومات', 'discount', 'discounts', 'DISCOUNT', 'CODE'];
    
        // Check if any of the specific offerWords is in the array
        foreach ($offerWords as $offer) {
            while (in_array($offer, $wordArray)) {
                $result = "offer";
                // Remove the offer word from the array
                $wordArray = array_diff($wordArray, [$offer]);
            }
        }
    
        // Check if any of the specific codeWords is in the array
        foreach ($codeWords as $code) {
            while (in_array($code, $wordArray)) {
                $result = "code";
                // Remove the code word from the array
                $wordArray = array_diff($wordArray, [$code]);
            }
        }
        $wordString = implode(' ', $wordArray);
        return [$result, $wordString];
    }
    public function update_images(){
       $advert=Advert::find(309);
       $parts = explode('/',$advert->image,5);  
       return $parts;
    }
   
}