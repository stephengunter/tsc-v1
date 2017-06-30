<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Course;

use App\Repositories\Courses;
use App\Repositories\Teachers;
use App\Repositories\Categories;
use App\Repositories\Terms;
use App\Repositories\Centers;
use App\Repositories\Weekdays;

use App\Http\Requests\Course\CourseRequest;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

use App\Events\CourseUpdated;


class ImportsController extends BaseController
{
   
    public function __construct(Courses $courses, Categories $categories, Teachers $teachers,
                                 Terms $terms , Centers $centers, Weekdays $weekdays,CheckAdmin $checkAdmin)
                               
    {
       
        //  $exceptAdmin=['activeCourses','details'];
        $exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
        
        $this->courses=$courses;
        $this->categories=$categories;
        $this->teachers=$teachers;
        $this->terms=$terms;
        $this->centers=$centers;
        $this->weekdays=$weekdays;

        $this->setCheckAdmin($checkAdmin);
       
      

	}
    public function indexOptions()
    {
        $termOptions=$this->terms->options();
        $allCategories=$this->categories->getAll()->get();       
        $categoryOptions=$this->categories->optionsConverting($allCategories);
        $centerOptions=$this->centers->options();

        $weekdayOptions=$this->weekdays->options();

        return response()
            ->json([
                'termOptions' => $termOptions,
                'categoryOptions' => $categoryOptions,
                'centerOptions' => $centerOptions,
                'weekdayOptions' => $weekdayOptions

            ]);

    }
   
    public function create()
    {   
        $termOptions=$this->terms->options();
        $centerOptions=$this->centers->options();
       
        return response()
            ->json([
                'termOptions' => $termOptions,
                'centerOptions' => $centerOptions
            ]);
    }
    public function store(CourseRequest $request)
    {
        $current_user=$this->currentUser();
        $removed=false;
        $updated_by=$current_user->id;

        $courseValues=$request->getCourseValues($updated_by,$removed);
        
        $categoryIds = $request->getCategoryIds();
        $teacherIds = $request->getTeacherIds();      
        
       
        $course = $this->courses->store($courseValues , $categoryIds, $teacherIds);
       
        return response()->json($course); 
      
    }
    public function show($id)
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('courses.details')
                    ->with([ 'menus' => $menus,
                              'id' => $id     
                        ]);
        }  
        $current_user=$this->currentUser();
        $course = Course::with('center','term','categories','teachers','classTimes')->findOrFail($id);
        
        $course->canEdit=$course->canEditBy($current_user);
        $course->canDelete=$course->canDeleteBy($current_user);

        foreach ($course->classTimes as $classTime) {
                $classTime->weekday;
        }
        $course->classTimes= $course->classTimes->sortBy('weekday_id')
                                                ->sortBy('on')->values()->all();;
        
        
        foreach ($course->teachers as $teacher) {
                $teacher->name=$teacher->getName();
        }
        return response()->json(['course' => $course]);
    }
    public function edit($id)
    {
        $course = Course::findOrFail($id);     
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        $validCenters=$current_user->admin->validCenters();
        $centerOptions=$this->centers->optionsConverting($validCenters);

        
        $publicCategories=false;
        $categoryOptions=$this->categories->options($publicCategories);
        $teacherOptions=$this->teachers->optionsByCenter($course->center_id);
        $termOptions=$this->terms->options();

        $course->categories;
        $course->teachers;
        foreach ($course->teachers as $teacher) {
                $teacher->name=$teacher->getName();
        }
       
        return response()
            ->json([
                'course' => $course,
                'centerOptions' => $centerOptions,
                'categoryOptions' => $categoryOptions,
                'teacherOptions' => $teacherOptions,
                'termOptions' => $termOptions,
            ]);    
    }
    public function update(CourseRequest $request, $id)
    {
        $course = Course::with('center')->findOrFail($id);     
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();         
        }
        $removed=false;
        $updated_by=$current_user->id;

        $courseValues=$request->getCourseValues($updated_by,$removed);       
        $categoryIds = $request->getCategoryIds();      
        $teacherIds = $request->getTeacherIds();   
       
        $course = $this->courses->update($courseValues , $categoryIds, $teacherIds ,$id);
        
        event(new CourseUpdated($course, $current_user));
        
        return response()->json(['course' => $course ]);
                
                    
               
    }
    // public function updateDisplayOrder(Request $request, $id)
    // {
    //         $course=Course::findOrFail($id); 
    //         $up=$request['up'];
    //         $num= rand(1, 10);
    //         if($up){
    //             $course->display_order += $num;
    //         }else{
    //             $course->display_order -= $num;
    //         }
            
    //         $course->save();
           
    //         return response()
    //             ->json([
    //                 'course' => $course
    //             ]);    

    // }

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
        $options=[];
        $request = request();
        $teacher_id=(int)$request->teacher; 
        if($teacher_id){
            $courseList=$this->courses->getByTeacher($teacher_id)->get();
            $options=$this->courses->optionsConverting($courseList);
            return response()  ->json(['options' => $options ]);   
        }
       
        
              
        $request = request();
        $term_id=(int)$request->term; 
        $center_id=(int)$request->center;

        $courseList=$this->courses->getAll()->where('term_id',$term_id)
                                     ->where('center_id',$center_id)
                                     ->get();
        $options=$this->courses->optionsConverting($courseList);    
        return response()->json(['options' => $options ]);     
    }

    // public function optionsByTeacher($teacher)
    // {
       
    //     $courseList=$this->courses->getByTeacher($teacher)->get();
       
    //     $options=$this->courses->optionsConverting($courseList);
    //        return response()
    //             ->json([
    //                 'options' => $options
    //             ]);   
        
    // }
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
