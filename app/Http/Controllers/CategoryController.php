<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Advert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
 

class CategoryController extends Controller
{
    
    
    public function get(){
        $category=Category::get();
      
          return response()->json(
             $category
              , 200);
      
        
    }
    public function store(Request $request){
        
        try{
            $validatecategory = Validator::make($request->all(), 
            [
                'name' => 'string|required|unique:categories,name',
                'image' => 'file|required|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                // seo columns
                'seo_image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                'seo_title' => 'nullable|string',
                'seo_description' => 'nullable|string',
                'seo_keywords' => 'nullable|string',
            ]);
            $validatecategory->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                  return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
            $validatecategory->sometimes('seo_image', 'mimetypes:image/vnd.wap.wbmp', function ($input) {
                return $input->file('seo_image') !== null && $input->file('seo_image')->getClientOriginalExtension() === 'wbmp';
          });
            if($validatecategory->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validatecategory->errors()
                ], 422);
            }
            $category = Category::create(array_merge(
                $validatecategory->validated()
                 ));
            if($request->hasFile('image') and $request->file('image')->isValid()){
                $category->image =$this->store_image($request->file('image')); 
            }
            if($request->hasFile('seo_image') and $request->file('seo_image')->isValid()){
                $category->seo_image =$this->store_image($request->file('seo_image')); 
            }
            $result=$category->save();
           
           if ($result){
                return response()->json(
                    
                Category::get()
                 , 201);
            }
            else{
                return response()->json(
                      null
                     , 422);
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
                ['id'=>'required|integer|exists:categories,id']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
                 'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 422);}
          
            $category=category::find($id);
          
            if($category)
            { 
                if($category->image !=null)  $this->deleteImage($category->image);
                if($category->seo_image !=null)  $this->deleteImage($category->seo_image);
                $adverts=$category->advert()->get();
               
                foreach($adverts as $advert){
                   
                     $advert->category()->dissociate();
                    
                      $advert->save();  
                   }
                  $category->save();
                $result= $category->delete();
                $category=Category::get();
                return response()->json($category, 200);
            }
    
           
                  
              
              
                return response()->json(null, 422);
          }
          catch (ValidationException $e) {
              return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
              return response()->json(['message' => 'An error occurred while deleting the categroy.'], 500);
          }
    }
    public function update(Request $request, $id){
        try{
            $input = [ 'id' =>$id ];
            $validate = Validator::make( $input,
            ['id'=>'required|integer|exists:categories,id']);
            if($validate->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => 'خطأ في التحقق',
                        'errors' => $validate->errors()
                    ], 422);
                }
            $validatecategory = Validator::make($request->all(), 
            [
                'name' => 'string',
                'image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                // seo columns
                'seo_image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                'seo_title' => 'nullable|string',
                'seo_description' => 'nullable|string',
                'seo_keywords' => 'nullable|string',
            ]);
            $validatecategory->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                  return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
            $validatecategory->sometimes('seo_image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                return $input->file('seo_image') !== null && $input->file('seo_image')->getClientOriginalExtension() === 'wbmp';
          });

            if($validatecategory->fails()){
                return response()->json([
                    'status' => false,
                     'message' => 'خطأ في التحقق',
                    'errors' => $validatecategory->errors()
                ], 422);
            }
            $category = Category::find($id);
            $category->update($validatecategory->validated() );
            if($request->hasFile('image') and $request->file('image')->isValid()){
                if($category->image !=null){
                    $this->deleteImage($category->image);
                }
                $category->image = $this->store_image($request->file('image')); 
            }
            if($request->hasFile('seo_image') and $request->file('seo_image')->isValid()){
                if($category->seo_image !=null){
                    $this->deleteImage($category->seo_image);
                }
                $category->seo_image = $this->store_image($request->file('seo_image')); 
            }
            
            $result = $category->update();
            
            if ($result){
                $category=Category::get();
                return response()->json($category, 200);
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
   
    // ////return codes and offers belonge to category_id 
    // public function show_data(Request $request,$id=null){
        
    //     try{
               
    //         $validate = Validator::make( $request->all(),
    //             [ 
                  
    //               'limit'=>'required|integer'
    //         ]);
    //         if($validate->fails()){
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'خطأ في التحقق',
    //                 'errors' => $validate->errors()
    //             ], 422);
    //         }
    //         $type = $request->query('type');
            
    //         if($id!== null) return $this->show($request,$id);
    //         else return $this->show_codes_offers($request);
            
    //     }
    //     catch (ValidationException $e) {
    //         return response()->json(['errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'An error occurred while obtaining  data.'], 500);
    //     }
    // }
    public function show($id){
        try {  
       
            
           
             
            $input = [ 'id' =>$id ];
            $validate = Validator::make( $input,
                ['id'=>'required|integer|exists:categories,id']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
                 'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 422);}
          
            $category=category::find($id);
          
            if($category)
            { 
               
            

                return response()->json([
               
                $category
                  
               ], 200);}
               else{
                return response()->json([
                    
                    'data'=> 'لم يتم العثور على الفئة'
                      
                   ], 422);
               }
            }    
          
          catch (ValidationException $e) {
              return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
              return response()->json(['message' => 'An error occurred while obtaining this categroy.'], 500);
          } 
        
    }
    // private function show_codes_offers(Request $request){
    //     try {  
         
    //          $offers= Advert::where([['type',0],['visible',1]])->latest()->take($request->limit)->get();
    //          $codes= Advert::where([['type',1],['visible',1]])->latest()->take($request->limit)->get();
    //            if($offers ||$codes){

    //             return response()->json([
               
    //             'codes'=>$codes,'offers'=>$offers
                  
    //            ], 200);}
    //            else{
    //             return response()->json([
                    
    //                 'data'=> 'this category could not be found'
                      
    //                ], 200);
    //            }
        
    
            
               
    //       }
    //       catch (ValidationException $e) {
    //           return response()->json(['errors' => $e->errors()], 422);
    //       } catch (\Exception $e) {
    //           return response()->json(['message' => 'An error occurred while obtaining  data.'], 500);
    //       } 
        
    // }
   
    
    //show adverts accourding to type and category_id or just accourding to type
    // public function get_data(Request $request){
     
    //     try{
              
    //         $validate = Validator::make( $request->all(),
    //             ['category_id'=>'integer|exists:categories,id',
    //               'type'=>'required|bool',
    //               'page'=>'required|integer',
    //               'limit'=>'required|integer'
    //         ]);
    //         if($validate->fails()){
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'خطأ في التحقق',
    //                 'errors' => $validate->errors()
    //             ], 422);
    //         }
    //         $type = $request->query('type');
            
    //         if($type!== null) return $this->show_adverts($request);
           
            
    //     }
    //     catch (ValidationException $e) {
    //         return response()->json(['errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'An error occurred while obtaining  data.'], 500);
    //     }
    // }
    // private function show_adverts(Request $request){
    //     try {  
       
            
    //         if($request->category_id==null ) {
    //             return $this->show_adverts_without_category_id($request);
    //         }
            
    //         $category=category::find($request->category_id);
     
    //         if($category)
    //         { 
    //             if($request->page ==1)$value=0;
    //             else{
    //                 $value=($request->page-1)*$request->limit;
    //             }
        
        
        
    //             $adverts=$category->advert()->where([['type',$request->type],['visible',1]])->offset($value)
    //                 ->latest()->take($request->limit)->get();
                    
          
    //             if($adverts ){

    //             return response()->json([
               
    //              'adverts'=>$adverts
                  
    //            ], 200);}
    //            else{
    //             return response()->json([
                    
    //                 'data'=> 'this category could not be found'
                      
    //                ], 422);
    //            }
    //         }    
    //       }
    //       catch (ValidationException $e) {
    //           return response()->json(['errors' => $e->errors()], 422);
    //       } catch (\Exception $e) {
    //           return response()->json(['message' => 'An error occurred while obtaining this categroy.'], 500);
    //       } 
        
    // }
    // private function show_adverts_without_category_id(Request $request){
    //     try {  
       
            
             
             
    //         if($request->page ==1)$value=0;
    //         else $value=($request->page-1)*$request->limit;
            
    //         $adverts=Advert::where([['type',$request->type],['visible',1]])->latest()->offset($value)
    //                 ->limit($request->limit)
    //                 ->get();
                  
    //         if($adverts ){

    //             return response()->json([
               
    //              'adverts'=>$adverts
                  
    //            ], 200);}
    //            else{
    //             return response()->json([
                    
    //                 'data'=> 'this category could not be found'
                      
    //                ], 200);
    //            }
                
    //       }
    //       catch (ValidationException $e) {
    //           return response()->json(['errors' => $e->errors()], 422);
    //       } catch (\Exception $e) {
    //           return response()->json(['message' => 'An error occurred while obtaining this data with out category id.'], 500);
    //       } 
        
    // }
    
   
    public  function deleteImage( $url){
        // Get the full path to the image
       
        $fullPath =$url;
         
        $parts = explode('/',$fullPath,7);
        $fullPath = public_path($parts[3].'/'.$parts[4]);
       
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
        $file->move(public_path('categories'), $imageName);

        // Get the full path to the saved image
        $imagePath = asset('categories/' . $imageName);
                
         
       
       return $imagePath;

    }

}