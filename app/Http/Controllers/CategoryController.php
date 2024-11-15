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
                $category->image =$this->storeImage($request->file('image'),'categories'); 
            }
            if($request->hasFile('seo_image') and $request->file('seo_image')->isValid()){
                $category->seo_image =$this->storeImage($request->file('seo_image'),'categories'); 
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
                $category->image = $this->storeImage($request->file('image'),'categories'); 
            }
            if($request->hasFile('seo_image') and $request->file('seo_image')->isValid()){
                if($category->seo_image !=null){
                    $this->deleteImage($category->seo_image);
                }
                $category->seo_image = $this->storeImage($request->file('seo_image'),'categories'); 
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
               
            

                return response()->json(
               
                $category
                  
               , 200);}
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
  

}