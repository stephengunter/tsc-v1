<?php

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Settings\HolidayRequest;

use App\Holiday;
use Carbon\Carbon;

use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

class HolidaysController extends BaseController
{
    public function __construct(CheckAdmin $checkAdmin) {
        
		$exceptAdmin=[];
        $allowVisitors=[];
		$this->setMiddleware( $exceptAdmin, $allowVisitors);


        $this->setCheckAdmin($checkAdmin);
	}
   
   public function index()
    {
        if(!request()->ajax()){
              
            return view('settings.holiday');
                    
        }

        $year=request()->get('year');
        $holidayList=Holiday::whereYear('date',$year)->orderBy('date')->get();
       
        if(count($holidayList)){
            $current_user=$this->currentUser();
            foreach ($holidayList as $holiday) {
                $holiday->canDelete = $holiday->canDeleteBy($current_user);
                $holiday->canEdit = $holiday->canEditBy($current_user);
            }
        }
        
       
        return response() ->json([ 'holidayList' => $holidayList ]);
    }
    public function create()
    {
        $holiday=Holiday::initialize();
        return response() ->json([ 'holiday' => $holiday ]);
    }
    public function store(HolidayRequest $request)
    {
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;
        $values=$request->getValues($updated_by);
      
        $end_date=$values['end_date'];

        if(!$end_date) {
            $holiday= Holiday::create($values);
            return response()->json($holiday); 
        }     
        
        $begin_date=Carbon::parse($values['date']);
        $end_date=Carbon::parse($end_date);
        $days= $begin_date->diffInDays($end_date,false);

        if($days<=0) 
        {
            $holiday= Holiday::create($values);
            return response()->json($holiday); 
        }

        $holiday=new Holiday();
        for($i=0;$i<=$days;$i++){
                $date=$begin_date->copy()->addDays($i);
                $values['date']=$date->format('Y-m-d');
                $holiday=new Holiday($values);               
                $holiday->save();
        }
       
        return response()->json($holiday);
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $holiday=Holiday::findOrFail($id); 
        if(!$holiday->canEditBy($current_user)){
            return  $this->unauthorized();    
        }
      
        return response()->json([
            'holiday' => $holiday
        ]);
        
    }
    public function update(HolidayRequest $request, $id)
    {
         $holiday=Holiday::findOrFail($id); 
         $current_user=$this->currentUser();

         if(!$holiday->canEditBy($current_user)){
            return  $this->unauthorized();
         }
         $updated_by=$current_user->id;
         $values=$request->getValues($updated_by);

         $holiday->update($values);

          return response()->json($holiday);
    }

    public function destroy($id)
    {
        $holiday=Holiday::findOrFail($id); 
        $current_user=$this->currentUser();
        if(!$holiday->canDeleteBy($current_user)){
            return  $this->unauthorized();
        }
     
        $holiday->delete();

        return response() ->json([ 'deleted' => true ]);
           
               
           
    }
    
}
