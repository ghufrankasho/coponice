<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\Category;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Query\Builder;

class AdvertController extends Controller
{
    public function index(Request $request=null){
        $category=Category::get();
        if($request!==null)
      {  $category_id = $request->query('category_id');
       if($category_id !==null)
        {$offers= Advert::where([['type',0],['category_id',$category_id],['visible',1]])->orderBy('main', 'desc')->orderBy('updated_at', 'desc')->take(12)->get()->toArray();
        $codes= Advert::where([['type',1],['category_id',$category_id],['visible',1]])->orderBy('main', 'desc')->orderBy('updated_at', 'desc')->take(12)->get()->toArray();
        $specials= Advert::where([['type',null],['category_id',$category_id],['visible',1]])->orderBy('main', 'desc')->orderBy('updated_at', 'desc')->take(12)->get()->toArray();
       }}
       else{$offers= Advert::where([['type',0],['visible',1]])->orderBy('main', 'desc')->orderBy('updated_at', 'desc')->take(12)->get()->toArray();
        $codes= Advert::where([['type',1],['visible',1]])->orderBy('main', 'desc')->orderBy('updated_at', 'desc')->take(12)->get()->toArray();
        $specials= Advert::where([['type',null],['visible',1]])->orderBy('main', 'desc')->orderBy('updated_at', 'desc')->take(12)->get()->toArray();
       }
        $slider1=Slider::where('type',1)->get();
        $slider2=Slider::where('type',2)->get();
        $slider3=Slider::where('type',3)->get();
        $slider4=Slider::where('type',4)->get();
        $slider5=Slider::where('type',5)->get();
        $special_name=Slider::where('type',null)->first();
     
        if( $special_name){
            $special_name=$special_name->makeHidden('image','type');
            if ($special_name->link==null){
                $specials=[]; 
             }}
        
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
                'special_name'=>$special_name,
                 ], 200);
        
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
                'type'=>'required|bool',
                'limit'=>'integer',
                'page'=>'integer'
            ]);
            if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validate->errors()
                ], 422);
            }
            $type = $request->query('type');

            if ($type === '1') {
               
                return $this->get_code($request);
            } elseif ($type === '0') {
                return $this->get_offer($request);
            } elseif (is_null($type)) {
                return $this->get_special($request);
            } else {
                return $this->get_offer($request);
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
    private function get_offer(Request $request){
            $data=$this->index($request);
            $responseData = json_decode($data->content(), true);
            $offers = $responseData['offers'];
            
        if($request->page ==1){
            $value=0;
           
            return response()->json(
                 $offers
                 , 200);
            
        }
        
        else{
            $value=($request->page-1)*$request->limit;
             
        }
        
        
        $advert= Advert::where([['type',0],['visible',1]])->offset($value)
                    ->limit($request->limit)->orderBy('main', 'desc')->orderBy('updated_at', 'desc')
                    ->get();
        $ads=array();
         
        foreach ($advert as $of) {
            // Assuming 'id' is a unique identifier for your offers
            $offerIds = array_column($offers, 'id');
            
            if (!in_array($of->id, $offerIds)) {
                array_push($ads, $of);
            }
        }
            
          return response()->json(
                 $ads
                 , 200);
    }
    private function get_code(Request $request){
         
        $data=$this->index($request);
      
        $responseData = json_decode($data->content(), true);
        $codes = $responseData['codes'];
        if($request->page ==1){
            $value=0;
            
            return response()->json(
                 $codes
                 , 200);
            
        }
        else{
            $value=($request->page-1)*$request->limit;
        }
        
        $advert= Advert::where([['type',1],['visible',1]])->offset($value)
                    ->limit($request->limit)->orderBy('main', 'desc')->orderBy('updated_at', 'desc')
                    ->get();
        $ads=array();
         
        foreach ($advert as $of) {
            // Assuming 'id' is a unique identifier for your offers
            $offerIds = array_column($codes, 'id');
            
            if (!in_array($of->id, $offerIds)) {
                array_push($ads, $of);
            }
        }
            
          return response()->json(
                 $ads
                 , 200);
    }
    private function get_special(Request $request){
            $data=$this->index($request);
            $responseData = json_decode($data->content(), true);
            $specials = $responseData['specials'];
       if($request->page ==1){
            $value=0;
            
            return response()->json(
                 $specials
                 , 200);
            
        }
                else{
                    $value=($request->page-1)*$request->limit;
                }
        
        
           $advert= Advert::where([['type',null],['visible',1]])->offset($value)
                    ->limit($request->limit)->orderBy('main', 'desc')->orderBy('updated_at', 'desc')
                    ->get();
        $ads=array();
         
        foreach ($advert as $of) {
            // Assuming 'id' is a unique identifier for your offers
            $offerIds = array_column($specials, 'id');
            
            if (!in_array($of->id, $offerIds)) {
                array_push($ads, $of);
            }
        }
            
          return response()->json(
                 $ads
                 , 200);
    }
    public function get_special_for_admin(Request $request){
        // return $advert= Advert::where('type',null)->get();
         
            
       if($request->page ==1){ $value=0;}
        else{
                $value=($request->page-1)*$request->limit;
            }
        $advert= Advert::where('type',null)->offset($value)
        ->limit($request->limit)->orderBy('main', 'desc')->orderBy('updated_at', 'desc')->get();
   
         
         
            
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
                $advert->image = $this->store_image($request->file('image')); 
            }
            if($request->hasFile('seo_image') and $request->file('seo_image')->isValid()){
                $advert->seo_image = $this->store_image($request->file('seo_image')); 
            }
            
            $result=$advert->save();
           if ($result){
                $adverts=Advert::where('type',$advert->type)->get();
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
                $adverts=Advert::where('type',$type)->get();
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
                    $advert->image = $this->store_image($request->file('image')); 
                }
                if($request->hasFile('seo_image') and $request->file('seo_image')->isValid()){
                    if($advert->seo_image !=null){
                        $this->deleteImage($advert->seo_image);
                    }
                    $advert->seo_image = $this->store_image($request->file('seo_image')); 
                }
                if($request->category_id != null){

                    $category=Category::find($request->category_id);
                    $advert->category()->associate($category);
                }
                
                $advert->save();
                $adverts=Advert::where('type',$advert->type)->get();
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
    public function deleteImage( $url){
        // Get the full path to the image
       
        $fullPath =$url;
         
       $parts = explode('/',$fullPath,7);
      
       $fullPath = public_path($parts[3].'/'.$parts[4]);
        //    return [$parts,$fullPath];
        // $fullPath = public_path($parts[5].'/'.$parts[6]);
        
        // Check if the image file exists and delete it
        if (file_exists($fullPath)) {
            unlink($fullPath);
            
            return true;
         }
         else return false;
    }
    public function store_image( $file){
        $extension = $file->getClientOriginalExtension();
           
        $imageName = uniqid() . '.' .$extension;
        $file->move(public_path('adverts'), $imageName);

        // Get the full path to the saved image
        $imagePath = asset('adverts/' . $imageName);
                
         
       
       return $imagePath;

    }
    public function suggest(Request $request){
        
      
        # this function suggest newly added adverts that belongto most commen categories
        # commen category is the category its advert has higher counter 
        if($request->type==1)
        {
             #setp 1 ::get adverts with highest counter
            $highest_offers=Advert::where([['type',$request->type],['visible',1]])->orderBy('counter', 'desc')->take(10)->get();
            $cat_offers=array();
            
            foreach($highest_offers as $offer){ 
                
                #step 2::get adverts that belongs to most commen category
                $offers=Advert::where([['category_id',$offer->category->id],['type',$request->type],['visible',1]])->orderBy('discount', 'desc')->latest()->take(10)->get();
                
                array_push($cat_offers , $offers); 
            }
        
            $final_offer=array();
            foreach($cat_offers as $arr_offer){
                foreach($arr_offer as $offer){
                    if(!in_array($offer,$final_offer) and $offer->visible and count($final_offer)<$request->limit)
                    {
                        array_push($final_offer,$offer);}
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
            ], 422);}
            $advert=Advert::find($request->id );
            $adverts=Advert::where([['category_id',$advert->category_id],['type',0],['visible',1]])->latest()->take($request->limit+1)->get()->toArray();
            
             
            $ads=array();
            foreach($adverts as $of)
            {if ($request->id !=$of['id']) {
                array_push($ads, $of);
            }}
            if(count($ads)<3)
            {  $offers= Advert::where('type',0)->inRandomOrder()->take($request->limit+1)->get()->toArray(); 
                 
                 
                foreach($offers as $of)
                {
                    if ($request->id !=$of['id'] and ! in_array($of,$ads)) {
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
    public function search($search,Request $request){
        try {
        
                $input = [ 'search' =>$search ];
               
                $validatesearch = Validator::make($input, 
                [ 'search' => 'required|string|min:3' ]); 
                
            if($validatesearch->fails()  ){
                    return response()->json([
                        'status' => false,
                         'message' => 'خطأ في التحقق',
                        'errors' => $validatesearch->errors()
                    ], 422);
                    }
            
             
           
            $adverts=array();
             
            $category= Category::where('name','LIKE', '%' . $search .'%')->with('advert')->first();
            
            if($category)
           { $adverts= $category['advert'];
               
           }
           
           
            
            $data = Advert::where('name','LIKE', '%' . $search .'%')
                ->orwhere('description','LIKE', '%' . $search .'%')->get();      
              
            
                if(count($data)>0)
            {
                
                if(count($adverts)>0){$data=$adverts->merge($data);}
            
                $result=array();
                
                $start = ($request->page-1)*$request->limit;;
            
            
                foreach($data as $advert){
                    
                    if(! in_array($advert,$result)  and $advert->type==$request->type and $advert->visible){
                        array_push($result , $advert);
                        
                    }
                }
            
                
                $slicedData = array_slice($result, $start, $request->limit);
                
                if ($slicedData)
                { return response()->json(
                            
                    $slicedData
                    , 200);  }
                else{
                    return response()->json([],204); 
                    
                }
            }
            else
            {
      
            $result=array();
            
            $start = ($request->page-1)*$request->limit;;
           
 
            foreach($adverts as $advert){
                 if(! in_array($advert,$result)  and $advert->type==$request->type){
                     array_push($result , $advert);
                 }
             }
              
             $slicedData = array_slice($result, $start, $request->limit);
              
             if ($slicedData)
               { return response()->json(
                         
                   $slicedData
                , 200);  }
            else{
                 return response()->json([],204); 
                
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