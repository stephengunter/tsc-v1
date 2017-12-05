<?php

namespace App\Http\Controllers\Signups;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Discounts\DiscountRequest;

use App\Discount;

use App\Repositories\Discounts;
use App\Repositories\Courses;
use App\Repositories\Centers;

use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

use Carbon\Carbon;
use Exception;

class DiscountsController extends BaseController
{
    
    protected $key='signups';

    public function __construct(Discounts $discounts, Courses $courses,Centers $centers)
    {
        $this->discounts=$discounts;
        $this->courses=$courses;
        $this->centers=$centers;
	}

    public function index()
    {
        
        $centerOptions=$this->centers->options();

        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('discounts.index')
                    ->with([
                            'menus' => $menus , 
                            'centerOptions'=> $centerOptions
                          ]);
        } 

        $current_user=$this->currentUser();

        $globalDiscount=$this->discounts->globalDiscount()->get();

        $center_id=(int)request()->center;
        $center=$this->centers->findOrFail($center_id);
        
        $canEdit=Discount::canCreate($current_user,$center);

        $centerDiscounts=$this->discounts->getAll()
                                ->where('center_id',$center_id)
                                ->orderBy('order','desc')
                                ->get();

        
        $discounts=$globalDiscount->merge($centerDiscounts);
                                 
        
        if(count($discounts)){
           
            foreach ($discounts as $discount) {
                $discount->canEdit = $discount->canEditBy($current_user);
                
            }
        }
                              
       
        
        return response()
        ->json([
            'discountList' => $discounts,
            'canEdit' => $canEdit
        ]);
         
                   
    }

    public function create()
    {
        $discount=Discount::initialize();
      
        return response()->json(['discount' => $discount]);

        
    }

    public function store(DiscountRequest $request)
    {
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

        $discount= Discount::create($values);
        return response() ->json($discount);
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $discount=$this->discounts->findOrFail($id);
        if(!$discount->canEditBy($current_user)){
            return  $this->unauthorized();    
        }
       
      
        return response()->json([  'discount' => $discount ]);
           
       
        
    }
    public function update(DiscountRequest $request, $id)
    {
        $current_user=$this->currentUser();
        $discount=$this->discounts->findOrFail($id);
        if(!$discount->canEditBy($current_user)){
            return  $this->unauthorized();    
        }

        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

        $discount->update($values);

        return response()->json(['discount' => $discount   ]);    
          
    }

    public function updateDisplayOrder(Request $form)
    {
        $current_user=$this->currentUser();

        $discounts=$form['discounts'];
        for($i = 0; $i < count($discounts); ++$i) {
            $discount=$discounts[$i];

            $id=$discount['id'];
            $order=$discount['order'];
            $updated_by=$current_user->id;

            $this->discounts->updateDisplayOrder($id,$order,$updated_by);
            
        }


        return response()->json(['success' => true]);

    }

    public function destroy($id)
    {
       $discount=$this->discounts->findOrFail($id); 
       $current_user=$this->currentUser();
       if(!$discount->canDeleteBy($current_user)){
            return  $this->unauthorized();      
       }
       
       $this->discounts->delete($id, $current_user->id);

        return response()->json([ 'deleted' => true ]);
          
               
           
    }

    public function options()
    {
        
        $date=null;
        try {
            $date = Carbon::parse(request()->date);
        }
        catch (Exception $err) {
            $date=Carbon::today();
        }

        $activeDiscounts=null;

        $course_id=(int)request()->course;
        $course=$this->courses->findOrFail($course_id);
        $activeDiscounts=$this->discounts->getValidDiscounts($course,$date);

        
        $options=$this->discounts->optionsConverting($activeDiscounts);
      

        return response()->json([ 'options' => $options ]);  
    }

    public function countTuition()
    {
        
        $course_id=(int)request()->course;
        $discount_id=(int)request()->discount;

        
        $date=null;
        try {
            $date = Carbon::parse(request()->date);
        }
        catch (Exception $err) {
            $date=Carbon::today();
        }

        $course=$this->courses->findOrFail($course_id);

        if(!$discount_id){ 
            $discount=new Discount();
            $discount->id=0;
            $discount->tuition=$course->tuition;
            $discount->points=0;
            return response()->json($discount); 
        } 

        
        $discount=$this->discounts->countTuition($course,$discount_id,$date);

        return response()->json($discount);  
    }

    



   



}
