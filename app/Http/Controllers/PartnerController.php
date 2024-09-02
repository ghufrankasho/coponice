<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Partner;

class PartnerController extends Controller
{
    public function get(){
        try{ 
                $partner=partner::latest()->get();
                
                return response()->json(
                    $partner
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
            $valdatepartner = Validator::make($request->all(), 
            [
                'name' => 'string|required',
                'image' => 'file|required|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                'link' => 'url:http,https|required',
                'visible'=>'bool',
            ]);
            $valdatepartner->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                  return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
            
            if($valdatepartner->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $valdatepartner->errors()
                ], 422);
            }
           $partner = partner::create(array_merge(
                $valdatepartner->validated()
                 ));
            if($request->hasFile('image') and $request->file('image')->isValid()){
               $partner->image =$this->store_image($request->file('image')); 
            }
            
            $result=$partner->save();
           
           if ($result){
                return response()->json(
                    
            partner::latest()->get()
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
                ['id'=>'required|integer|exists:partners,id']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
                 'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 422);}
          
           $partner=partner::find($id);
          
        if($partner)
            { 
                if($partner->image !=null)  $this->deleteImge($partner->image);
                
                $resut= $partner->delete();
                $partners=partner::latest()->get();
                return response()->jon($partners, 200);
            }
    
           
                  
              
              
                return response()->json(null, 422);
          }
          catch (ValidationException $e) {
              return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
              return response()->json(['message' => 'An error occurred while deleting the partners.'], 500);
          }
    }
    public function update(Request $request, $id){
        try{
            $input = [ 'id' =>$id ];
            $validate = Validator::make( $input,
            ['id'=>'required|integer|exists:partners,id']);
            if($validate->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => 'خطأ في التحقق',
                        'errors' => $validate->errors()
                    ], 422);
                }
            $valdatepartner = Validator::make($request->all(), 
            [
                'name' => 'string',
                'image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                'link' => 'url:http,https',
                'visible'=>'bool',
            ]);
            $valdatepartner->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                  return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
           

            if($valdatepartner->fails()){
                return response()->json([
                    'status' => false,
                     'message' => 'خطأ في التحقق',
                    'errors' => $valdatepartner->errors()
                ], 422);
            }
            $partner = partner::find($id);
            $partner->update($valdatepartner->validated() );
            
           if($request->hasFile('image') and $request->file('image')->isValid()){
                if($partner->image !=null){
                        $this->deleteImge($partner->image);
                    }
                $partner->image = $this->store_image($request->file('image')); 
            }
            
            
            $result = $partner->update();
            
            if ($result){
                
               $partners=partner::latest()->get();
                
               return response()->jon($partners, 200);
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
                ['id'=>'required|integer|exists:partners,id']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
              'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 422);}
            
           $partner=partner::find($request->id);
     
        if($partner)
            { 
               
              

                return response()->json(
               
                    $partner
                  
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
            $file->move(public_path('partners'), $imageName);
    
            // Get the full path to the saved image
            $imagePath = asset('partners/' . $imageName);
                    
             
           
           return $imagePath;
    
        }
    }