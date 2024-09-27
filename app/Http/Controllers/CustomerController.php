<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Query\Builder;
class CustomerController extends Controller
{
   public function store(Request $request){
        
         
        try{
           
            $validatecustomer =Validator::make($request->all(), [
                'email' => 'required|string|unique:customers,email',
                'phone' => 'required|min:10|numeric|unique:customers,phone',
                ] );
          
            if($validatecustomer->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validatecustomer->errors()
                ], 409);
            }
             $customer = Customer::create(array_merge(
                $validatecustomer->validated()
                 ));
           
            $result=$customer->save();
           if ($result){
                return response()->json([
               
                'data'=> $customer
                 ], 201);
            }
            else{
                return response()->json([
                    'status' => false,
                    'message' =>  ' Failed to add customer ',
                    'data'=> null
                     ], 422);
            }

        }catch (ValidationException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $e->errors(),
        ], 422);
    }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
       
        
    }
 
  
    
 public function index(){
       
        try {  
             
            
            $customer=Customer::latest()->get();
            if($customer){
                return response()->json(
             
                  $customer
                  
               , 200); 
            }
             else{
                return response()->json([
                    
                    'data'=> null
                     ], 401);
            }
           
            }
   
        catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while requesting this advert .'], 500);
          }
    }
}