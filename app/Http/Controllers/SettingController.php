<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Setting;
class SettingController extends Controller
{
    public function get(){
        try{ 
                $Setting=Setting::latest()->get();
                
                return response()->json(
                    $Setting
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
            $valdateSetting = Validator::make($request->all(), 
            [
                'is_hidden' => 'bool',
                'title' => 'string|required',
                'key' => 'string|required',
                'value_default' => 'string|required',
                'value_actual' => 'string|required',
                'description' => 'string',
               
            ]);
 
            
            if($valdateSetting->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $valdateSetting->errors()
                ], 422);
            }
           $Setting = Setting::create(array_merge(
                $valdateSetting->validated()
                 ));
           
            
         
           
           if ( $Setting){
                return response()->json(
                    
            Setting::latest()->get()
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
                ['id'=>'required|integer|exists:Settings,id']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
                 'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 422);}
          
           $Setting=Setting::find($id);
          
        if($Setting)
            { 
                 
                $result= $Setting->delete();
                $Settings=Setting::latest()->get();
                if($result)return response()->json($Settings, 200);
            }
    
           
                  
              
              
            return response()->json(null, 422);
          }
          catch (ValidationException $e) {
              return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
              return response()->json(['message' => 'An error occurred while deleting this Setting.'], 500);
          }
    }
    public function update(Request $request, $id){
        try{
            $input = [ 'id' =>$id ];
            $validate = Validator::make( $input,
            ['id'=>'required|exists:settings,id']);
            if($validate->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => 'خطأ في التحقق',
                        'errors' => $validate->errors()
                    ], 404);
                }
            $valdateSetting = Validator::make($request->all(), 
            [
                'is_hidden' => 'nullable|bool',
                
                
                'value_actual' => 'nullable|string',
                'description' => 'nullable|string',
            ]);
           
           

            if($valdateSetting->fails()){
                return response()->json([
                    'status' => false,
                     'message' => 'خطأ في التحقق',
                    'errors' => $valdateSetting->errors()
                ], 422);
            }
            $Setting = Setting::find($id);
            
            $result= $Setting->update($valdateSetting->validated() );
            
          
            
        
            
            if ($result){
                
               $Settings=Setting::latest()->get();
                
               return response()->json($Settings, 200);
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
    public function show($data){
        try {  
       
            
            $input = [ 'id' =>$data ,
                       'key' =>$data];
           if (gettype(json_decode($data))=='integer')
            {
               
                $validateid= Validator::make( $input,
                ['id'=>'required|exists:Settings,id']);
                
                if($validateid->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => 'خطأ في التحقق',
                        'errors' => $validateid->errors()
                    ], 404);
                    
                }
            $Setting = Setting::find($data) ;
            if($Setting)
            { 
                return response()->json(
            
                    $Setting
                
                , 200);}
        else{
                return response()->json([
                    
                    'data'=> 'لم يتم العثور على البيانات'
                    
                ], 422);
            }
            }
            
            $validatekey= Validator::make( $input,
                ['key'=>'required|exists:Settings,key']);
         
            if($validatekey->fails()){
                return response()->json([
                    'status' => false,
                     'message' => 'خطأ في التحقق',
                    'errors' => $validatekey->errors()
                ], 404);
                
            }
            $Setting = Setting::where('key',$data)->first();
          
            if($Setting)
                { 
                    return response()->json(
                
                        $Setting
                    
                    , 200);}
            else{
                    return response()->json([
                        
                        'data'=> 'لم يتم العثور على البيانات'
                        
                    ], 422);
                }
        }    
          
        catch (ValidationException $e) {
              return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
              return response()->json(['message' => 'An error occurred while obtaining this data.'], 500);
          } 
    }
}