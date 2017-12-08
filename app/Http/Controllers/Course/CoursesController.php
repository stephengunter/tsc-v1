<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Course;
use App\Center;
use App\Schedule;

use App\Services\Course\CourseService;


use App\Http\Requests\Course\CourseRequest;

use App\Support\Helper;
use Carbon\Carbon;




class CoursesController extends BaseController
{
    protected $key='courses';
   
    public function __construct(CourseService $courseService)
    {
        $this->courseService=$courseService;
    }
    public function indexOptions()
    {
        
        $termOptions=$this->courseService->termOptions();
        $centerOptions=$this->courseService->centerOptions();
             
        $categoryOptions=$this->courseService->categoryOptions();
        $weekdayOptions=$this->courseService->weekdayOptions();

        return response()
            ->json([
                'termOptions' => $termOptions,
                'categoryOptions' => $categoryOptions,
                'centerOptions' => $centerOptions,
                'weekdayOptions' => $weekdayOptions
            ]);

    }
    public function index()
    {
        
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('courses.index')
                    ->with(['menus' => $menus]);
        }      
        
        
        $params = request()->toArray();

        $with=['center','categories','teachers','classTimes'];
        $courseList=$this->courseService->index($params,$with);
                           
        $hasReviewed =array_key_exists('reviewed', $params);
        if($hasReviewed){
            $reviewed=(bool)$params['reviewed'];
            $courseList=$courseList->where('reviewed',$reviewed);
        };
      
        $courseList=$courseList->filterPaginateOrder();
       
        $canEditNumber=false;
        $centerId=Helper::getIntegerByKey($params, 'center');
        if($centerId){
            $center=Center::findOrFail($centerId);
            $canEditNumber=$this->canAdminCenter($center);
        }
        
        
        foreach ($courseList as $course) {
            $course->populateViewData($canEditNumber);
            
        }

        return response() ->json([
                                    'model' => $courseList, 
                                    'canEditNumber' => $canEditNumber 
                                 ]);  
       
    }
    public function create()
    {   
        abort(404);
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('courses.create')
                    ->with(['menus' => $menus]);
        }
       
        

        return $this->createCourse();
        
    }
    private function createCourse()
    {
        dd('err');
        // $categoryOptions=$this->getCategoryOptions();    
        // $centerOptions=$this->getCenterOptions();    
        // $termOptions=$this->terms->options();

        // $center_id=$centerOptions[0]['value'];
        // $course= Course::initialize($center_id);
        // $course['term_id']=$termOptions[0]['value'];

        // $teacherOptions=$this->teachers->optionsByCenter($center_id);
        
       
        // return response()
        //     ->json([
        //         'course' => $course,
        //         'centerOptions' => $centerOptions,
        //         'categoryOptions' => $categoryOptions,
        //         'teacherOptions' => $teacherOptions,
        //         'termOptions' => $termOptions
                
        //     ]);
    }
    private function createGroupCourse($parent_id)
    {
        dd('err');
        // $parentCourse=Course::find($parent_id); 
        // if(!$parentCourse) return $this->createCourse();

        // $center_id=$parentCourse->center_id;
        
        // $course= Course::initialize( 0 ,$parentCourse);

        // $term_id=$course['term_id'];
        // $groupCourses= $this->courses->getGroupCourses($term_id,$center_id)->get();
                                                      
        // $with_empty=true;
        // $groupOptions=$this->courses->optionsConverting($groupCourses,$with_empty);

        // $centerOptions=$this->getCenterOptions();
        // $categoryOptions=$this->getCategoryOptions();
        // $teacherOptions=$this->teachers->optionsByCenter($center_id);
        
        // $termOptions=$this->terms->options();

        // return response()->json([
                        
        //                     'course' => $course,
        //                     'centerOptions' => $centerOptions,
        //                     'categoryOptions' => $categoryOptions,
        //                     'teacherOptions' => $teacherOptions,
        //                     'termOptions' => $termOptions,
        //                     'groupOptions' => $groupOptions,
        //                     'groupCourses' => $groupCourses
                            
        //                 ]);
    }

    

    public function store(CourseRequest $request)
    {
        $course=$this->createOrUpdate($request);

        return response()->json($course);
    }
    
    private function createOrUpdate(CourseRequest $request, Course $course=null)
    {
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;
        $removed=false;

        $courseValues=$request->getCourseValues($updated_by,$removed);
        
        $teacherIds = $request->getTeacherIds();
        $categoryIds = $request->getCategoryIds(); 

        if($course) return $this->courseService->update($course , $courseValues , $categoryIds, $teacherIds);

        return $this->courseService->store($courseValues,$categoryIds,$teacherIds);
        
    }
    public function show($id)
    {
        $course = Course::findOrFail($id);
        dd($course->canSignup());
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('courses.details')
                    ->with([ 'menus' => $menus,
                              'id' => $id     
                        ]);
        }

        

        $current_user=$this->currentUser();
        $with=['status','center','term','categories','teachers','classTimes'];
        $course = Course::with($with)->findOrFail($id);

        $course->populateViewData();
        
        $course->canEdit=$course->canEditBy($current_user);
        $course->canDelete=$course->canDeleteBy($current_user);
        $course->canReview=$course->canReviewBy($current_user);

        
        $course->canSignup=$course->canSignup();
        
        
        return response()->json(['course' => $course]);
    }
    public function edit($id)
    {
        $course = Course::with(['categories','teachers'])->findOrFail($id);     
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

       

        $course->teachers->each(function ($item) {
             $item->getName();
        });

        $course->begin_date=Helper::checkDateString($course->begin_date);
        $course->end_date=Helper::checkDateString($course->end_date);


        $termOptions=$this->courseService->termOptions();
        $centerOptions=$this->courseService->centerOptions();
        $categoryOptions=$this->courseService->categoryOptions();

        $teacherOptions=$this->courseService->teacherOptions($course->center_id);

        return response()
            ->json([
                'course' => $course,

                'termOptions' => $termOptions,
                'centerOptions' => $centerOptions,
                'teacherOptions' => $teacherOptions,
                'categoryOptions' => $categoryOptions,
                
              
            ]);
   
                          
    }
   
    public function update(CourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);     
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();         
        }
       
        $course=$this->createOrUpdate($request,$course);
        
        return response()->json($course);     
               
    }
    public function updateNumbers(Request $form)
    {
        $current_user=$this->currentUser();
        $courses=$form['courses'];
        
        $errors=[];

        for($i = 0; $i < count($courses); ++$i) {
            $course=$courses[$i];

            $id=$course['id'];
            $custom_number=trim($course['custom_number']);
            $updated_by=$current_user->id;
 
            if(!$custom_number) continue;

            $course=$this->courses->updateNumber($id,$custom_number,$updated_by);
            if(!$course) array_push($errors, $id);
            
            
        }

        if(count($errors))  return response()->json(['error' => $errors,'code' => 422 ], 422);


        return response()->json(['success' => true]);

    }

    public function updatePhoto(Request $request, $id)
    {
        $course=Course::findOrFail($id); 
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
           return  $this->unauthorized();   
        }
            
        $course->photo_id=$request['photo_id'];
        $course->updated_by=$current_user->id;
        $course->save();

         
        return response()->json(['saved' => true ]);      

    }
    
   
    public function destroy($id)
    {
        $current_user=$this->currentUser();
        $course=$this->courses->findOrFail($id);
        if(!$course->canDeleteBy($current_user)){
           return  $this->unauthorized();     
        }
        $this->courses->delete($id,$current_user->id);

        return response()->json([ 'deleted' => true ]);
           
    }
    

    public function options()
    {
        $courseList=[];
        $options=[];
        
        
        $params = request()->toArray();
        
        $with_empty=false;
        $options=$this->courseService->options($params,$with_empty);
       
          
        return response()->json([ 'options' => $options ]);                            
                                 
    }

    
    public function search()
    {
        $courseList=[];
        $request=request();
        $keyword=$request->name;  
        
        $courseList=$this->courses->searchByName($keyword);
        return response()
                ->json([
                    'courseList' => $courseList->get()
                ]);  
        
    }
    public function activeCourses()
    {
        $request = request();
        $categoryId=(int)$request->category;       
        $centerId=(int)$request->center;
        $courseList=$this->courses->getActiveCourses()->get();

        foreach ($courseList as $course) {
            $course->photo= $course->photo();
              foreach ($course->classTimes as $classTime) {
                $classTime->weekday;
             }
         }

        return response()
                ->json([
                    'courseList' => $courseList
                ]);  

    }

    
    
    
    
    
    
}
