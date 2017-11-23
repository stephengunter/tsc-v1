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
        dd(new Carbon('2017-'));
        $centerOptions=$this->centers->options();

        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('discounts.index')
                    ->with([
                            'menus' => $menus , 
                            'centerOptions'=> $centerOptions
                          ]);
        } 

        $center_id=(int)request()->center;
        $center=$this->centers->findOrFail($center_id);
        
        $canEdit=false;

        $discountList=$this->discounts->getAll()
                                    ->where('center_id',$center_id)
                                    ->orderBy('order','desc')
                                    ->get();
        $current_user=$this->currentUser();                          
        if(count($discountList)){
            
            $canEdit=$discountList[0]->canEditBy($current_user);
        }else{
            $canEdit=Discount::canCreate($current_user,$center);
        }
                              
        // if(count($discountList)){
        //     $current_user=$this->currentUser();
        //     foreach ($discountList as $discount) {
        //         $discount->canDelete = $discount->canDeleteBy($current_user);
        //         $discount->canEdit = $discount->canEditBy($current_user);
        //         $discount->identity = $discount->identity();
        //     }
        // }
        
        return response()
        ->json([
            'discountList' => $discountList,
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
        $course_id=(int)request()->course;
        $date_string=request()->date;



        $course=$this->courses->findOrFail($course_id);

        $activeDiscounts=$this->discounts->getValidDiscounts($course);
        
        $options=$this->discounts->optionsConverting($activeDiscounts);
      

        return response()->json([ 'options' => $options ]);  
    }

    public function countTuition()
    {
        
        $course_id=(int)request()->course;
        $discount_id=(int)request()->discount;

        $course=$this->courses->findOrFail($course_id);

        if(!$discount_id){ 
            $discount=new Discount();
            $discount->id=0;
            $discount->tuition=$course->tuition;
            $discount->points=0;
            return response()->json($discount); 
        } 

        
        $discount=$this->discounts->countTuition($course,$discount_id);

        return response()->json($discount);  
    }

    



   



}
