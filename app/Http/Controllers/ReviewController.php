<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Review;
 

class ReviewController extends Controller
{
    public function get(){
        try{ 
                $Review=Review::latest()->get();
                
                return response()->json(
                    $Review
                    , 200);
            }
        catch (ValidationException $e) {
                return response()->json(['errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                return response()->json(['message' => 'An error occurred while getting  the data.'], 500);
            }    
      
        
    }
    public function store(Request $request){
        
        try{
            $valdateReview = Validator::make($request->all(), 
            [
                'name' => 'string|required',
                'review' => 'string|required',
                'gender' => 'string|required',
                // 'image' => 'file|required|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                'date' => 'date|required',
                'visible'=>'bool',
            ]);
            $valdateReview->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                  return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
            
            if($valdateReview->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $valdateReview->errors()
                ], 422);
            }
           $Review = Review::create(array_merge(
                $valdateReview->validated()
                 ));
            if($request->hasFile('image') and $request->file('image')->isValid()){
               $Review->image =$this->store_image($request->file('image')); 
            }
            
            $result=$Review->save();
           
           if ($result){
                return response()->json(
                    
            Review::latest()->get()
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
                ['id'=>'required|integer|exists:reviews,id']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
                 'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 422);}
          
           $Review=Review::find($id);
          
        if($Review)
            { 
                if($Review->image !=null)  $this->deleteImge($Review->image);
                
                $resut= $Review->delete();
                $reviews=Review::latest()->get();
                return response()->jon($reviews, 200);
            }
    
           
                  
              
              
                return response()->json(null, 422);
          }
          catch (ValidationException $e) {
              return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
              return response()->json(['message' => 'An error occurred while deleting this review.'], 500);
          }
    }
    public function update(Request $request, $id){
        try{
            $input = [ 'id' =>$id ];
            $validate = Validator::make( $input,
            ['id'=>'required|integer|exists:reviews,id']);
            if($validate->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => 'خطأ في التحقق',
                        'errors' => $validate->errors()
                    ], 422);
                }
            $valdateReview = Validator::make($request->all(), 
            [
              'name' => 'string',
                'review' => 'string',
                'gender' => 'string',
                // 'image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                'date' => 'date',
                'visible'=>'bool',
            ]);
            $valdateReview->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                  return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
           

            if($valdateReview->fails()){
                return response()->json([
                    'status' => false,
                     'message' => 'خطأ في التحقق',
                    'errors' => $valdateReview->errors()
                ], 422);
            }
            $Review = Review::find($id);
            $Review->update($valdateReview->validated() );
            
           if($request->hasFile('image') and $request->file('image')->isValid()){
                if($Review->image !=null){
                        $this->deleteImge($Review->image);
                    }
                $Review->image = $this->store_image($request->file('image')); 
            }
            
            
            $result = $Review->update();
            
            if ($result){
                
               $reviews=Review::latest()->get();
                
               return response()->jon($reviews, 200);
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
    public function show(Request $request){
        try {  
       
            
          
            $validate = Validator::make( $request->all(),
                ['id'=>'required|integer|exists:reviews,id']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
              'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 422);}
            
           $Review=Review::find($request->id);
     
        if($Review)
            { 
               
              

                return response()->json(
               
                    $Review
                  
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
              return response()->json(['message' => 'An error occurred while obtaining this data.'], 500);
          } 
    }
    public function deleteImage( $url){
            // Get the full path to the image
           
            $fullPath =$url;
             
           $parts = explode('/',$fullPath,7);
            $fullPath = public_path($parts[5].'/'.$parts[6]);
            
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
            $file->move(public_path('reviews'), $imageName);
    
            // Get the full path to the saved image
            $imagePath = asset('reviews/' . $imageName);
                    
             
           
           return $imagePath;
    
        }
    }