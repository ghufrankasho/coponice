<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

use App\Models\Slider;
use Ramsey\Uuid\Type\Integer;

class SliderController extends Controller
{
 
    public function store(Request $request){
        
        try{
            $validateslider = Validator::make($request->all(), 
            [
                'link' => 'url:http,https|required',
                'type' => 'required|in:1,2,3,4,5',
                'visible'=>'bool',
                'alt'=>'string',
                'expire_date'=>'date',
                'image' => 'file|required|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
            ]);
            $validateslider->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                  return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
            if($validateslider->fails()){
                return response()->json([
                    'status' => false,
                     'message' => 'خطأ في التحقق'
                     ,
                    'errors' => $validateslider->errors()
                ], 422);
            }
           
            $slider = Slider::create(array_merge(
                $validateslider->validated()
                 ));
         
            $sliders=Slider::where('type',$slider->type)->get();
            
            $slider->sorting=count($sliders);   
             
            if($request->hasFile('image') and $request->file('image')->isValid()){
                $slider->image = $this->storeImage($request->file('image'),'sliders'); 
            }
           
            $result=$slider->save();
            $type=$slider->type;
          
           if ($result){
                $this->sort_sliders($type);
                return response()->json( [
                    'result'=>"data added successfully"
                   ] , 201);
            }
            else{
                return response()->json(null, 204);
            }

        }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
       
        
    }
    public function update(Request $request, $id){
        try{
            
            
            $input = [ 'id' =>$id ];
            $validate = Validator::make( $input,
            ['id'=>'required|integer|exists:sliders,id']);
            if($validate->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => 'خطأ في التحقق',
                        'errors' => $validate->errors()
                    ], 422);
                }
            
             $validateslider = Validator::make($request->all(), [
                'link' => 'url:http,https',
                'type' => 'in:1,2,3,4,5',
                 'alt'=>'nullable|string',
                'visible'=>'bool',
                'expire_date'=>'date',
                
                
                'image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
            ]);
            
            $validateslider->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
               if($validateslider->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => 'خطأ في التحقق',
                        'errors' => $validate->errors()
                    ], 422);
                }  
                         
            $slider = Slider::find($id);
            
         
          if($slider)  
          {  $slider->update($validateslider->validated());
            if($request->hasFile('image') and $request->file('image')->isValid()){
                if($slider->image !=null){
                    $this->deleteImage($slider->image);
                }
                $slider->image = $this->storeImage($request->file('image'),'sliders'); 
            }
            $type=$slider->type;
          
            $result=$slider->save();
            if ($result){
                $this->sort_sliders($type);
            //    $sliders=Slider::where('visible',1)->get();
                return response()->json( [
                    'result'=>"data updated successfully"
                   ] , 200);
            }
           
          }
            else{
                return response()->json(null, 204);
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
                ['id'=>'required|integer|exists:sliders,id']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
               'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 422);}
          
            $slider=Slider::find($id);
           
            if($slider )
            { 
                if($slider->image!=null) 
                {
                    $this->deleteImage($slider->image);
                }
                $type=$slider->type;
                $result= $slider->delete();
                if($result)
                 {
                    $this->sort_sliders($type);
                   
             
                return response()->json(
                    [
                        'result'=>"data deleted successfully"
                       ]
                 , 200);}
                
            }
    
              
                return response()->json(null, 204);
          }
          catch (ValidationException $e) {
              return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
              return response()->json(['message' => 'An error occurred while deleting the categroy.'], 500);
          }
    }
    public function show($type){
       
        try {  
           
            $input = [ 'type' =>$type ];
            $validate = Validator::make( $input,
                ['type' => 'required|in:1,2,3,4,5']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validate->errors()
            ], 404);}
          
            $slider=Slider::where('type',$type)->get();
            
            return response()->json(
            
             $slider
                  
               , 200);
            }
   
        catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while requesting this slider .'], 500);
          }
    }
    public function index(){
        
          try{
           
            
            $sliders=Slider::where('type','!=',null)->get();
           if ($sliders){
                return response()->json( $sliders
                 , 200);
            }
            else{
                return response()->json(null, 204);
            }

        }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        
    }
    public function get(Request $request){
       
        try {  
          
            $validate = Validator::make( $request->all(),
                ['type'=>'nullable|exists:sliders,type']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
               'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 404);}
             
            if($request->type != null){
                return $this->show($request->type);
             
            }
            else{
                return $this->index();
            }
        }
        catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while requesting this slider .'], 500);
          }
    }
    public function show_slider($id){
        try {  
             
            $input = [ 'id' =>$id ];
            $validate = Validator::make( $input,
                ['id'=>'required|integer|exists:sliders,id']);
            if($validate->fails()){
            return response()->json([
                'status' => false,
               'message' => 'خطأ في التحقق',
                'errors' => $validate->errors()
            ], 422);}
          
            $slider=Slider::find($id);
             
            if($slider )
            { 
                
             
                return response()->json(
                 $slider
                 , 200);}

            return response()->json(null, 204);
          }
          catch (ValidationException $e) {
              return response()->json(['errors' => $e->errors()], 422);
          } catch (\Exception $e) {
              return response()->json(['message' => 'An error occurred while deleting the categroy.'], 500);
          }
    }
    private function sort_sliders($type){
        
        $sliders=Slider::where('type',$type)->orderby('sorting','ASC')->get();
        $i=1;
        foreach($sliders as $slider){
            $slider->sorting=$i;
            $slider->save();
            $i++;
        }
    }
    public function reorderSliders(Request $request)
    {
    
        $data = $request->all();
        $max=count(Slider::where('type',$data[0]['type'])->get());
        // Validation to ensure the structure is correct
        $validateslider = Validator::make( $request->all(),
           [ '*.id' => 'required|exists:sliders,id',
           '*.sorting' => [
            'required',
            'integer',
            'min:1',
            function ($attribute, $value, $fail) use ($max) {
                if ($value > $max) {
                    $fail("The {$attribute} يجب ان تكون اقل او تساوي عدد السلايدر التابعة لنوع معين .");
                }
            },
        ],
             
            '*.type' => 'required|integer|exists:sliders,type',] // Ensure the type exists in sliders
        );
        if($validateslider->fails()){
            return response()->json([
                'status' => false,
                 'message' => 'خطأ في التحقق'
                 ,
                'errors' => $validateslider->errors()
            ], 422);
        }
        
        // Check for duplicate sorting values
        $sortingValues = array_column($data, 'sorting');
        
      
       
        if (count($sortingValues) !== count(array_unique($sortingValues))) {
            return response()->json(['errors' => 'لا يجب أن يكون هناك أي قيم مكررة'], 422);
        }
       
       
        try {
            foreach ($data as $slider) {
                DB::table('sliders')->where('id', $slider['id'])->update(['sorting' => $slider['sorting']]);
            }
            return response()->json(['data' => 'تم الترتيب بنجاح.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while sorting sliders.'], 500);
        }
    }
    
    
    
    
  
    
}