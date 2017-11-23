<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Course;
use App\Center;
use App\Schedule;

use App\Repositories\Courses;
use App\Repositories\Teachers;
use App\Repositories\Categories;
use App\Repositories\Terms;
use App\Repositories\Centers;
use App\Repositories\Weekdays;

use App\Http\Requests\Course\CourseRequest;

use App\Support\Helper;
use Carbon\Carbon;




class CoursesController extends BaseController
{
    protected $key='courses';
    public function __construct(Courses $courses, Categories $categories, Teachers $teachers,
                                 Terms $terms , Centers $centers, Weekdays $weekdays)
                               
    {
       
        $this->courses=$courses;
        $this->categories=$categories;
        $this->teachers=$teachers;
        $this->terms=$terms;
        $this->centers=$centers;
        $this->weekdays=$weekdays;

	}
    public function indexOptions()
    {
        
        $termOptions=$this->terms->options();

        $allCategories=$this->categories->privateCategories()->get();       
        $categoryOptions=$this->categories->optionsConverting($allCategories);

        $centerOptions=$this->centers->options();
        $weekdayOptions=$this->weekdays->options();

        $groupCategory=$this->categories->groupCategory();

        return response()
            ->json([
                'termOptions' => $termOptions,
                'categoryOptions' => $categoryOptions,
                'centerOptions' => $centerOptions,
                'weekdayOptions' => $weekdayOptions,
                'groupCategory' => $groupCategory

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
        $courseList=$this->courses->index($params,$with);
                           
        $hasReviewed =array_key_exists('reviewed', $params);
        if($hasReviewed){
            $reviewed=(bool)$params['reviewed'];
            $courseList=$courseList->where('reviewed',$reviewed);
        };
      
        $courseList=$courseList->filterPaginateOrder();

        
        $parentId=Helper::getIntegerByKey($params, 'parent');
        $parentCourse=null;
        if($parentId) $parentCourse=Course::with($with)->find($parentId);
        if($parentCourse) $parentCourse->populateViewData();
       
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
                                    'parentCourse' => $parentCourse,
                                    'canEditNumber' => $canEditNumber 
                                 ]);  
       
    }
   
    private function getCenterOptions()
    {
        $validCenters=$this->canAdminCenters();
        return $this->centers->optionsConverting($validCenters);
    }
    private function getCategoryOptions()
    {
        return $this->categories->options();
       
    }
    public function create()
    {   
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('courses.create')
                    ->with(['menus' => $menus]);
        }
       
        $parent=(int)request()->parent; 
        if($parent) return $this->createGroupCourse($parent);

        return $this->createCourse();
        
    }
    private function createCourse()
    {
        $categoryOptions=$this->getCategoryOptions();    
        $centerOptions=$this->getCenterOptions();    
        $termOptions=$this->terms->options();

        $center_id=$centerOptions[0]['value'];
        $course= Course::initialize($center_id);
        $course['term_id']=$termOptions[0]['value'];

        $teacherOptions=$this->teachers->optionsByCenter($center_id);
        
       
        return response()
            ->json([
                'course' => $course,
                'centerOptions' => $centerOptions,
                'categoryOptions' => $categoryOptions,
                'teacherOptions' => $teacherOptions,
                'termOptions' => $termOptions
                
            ]);
    }
    private function createGroupCourse($parent_id)
    {
        $parentCourse=Course::find($parent_id); 
        if(!$parentCourse) return $this->createCourse();

        $center_id=$parentCourse->center_id;
        
        $course= Course::initialize( 0 ,$parentCourse);

        $term_id=$course['term_id'];
        $groupCourses= $this->courses->getGroupCourses($term_id,$center_id)->get();
                                                      
        $with_empty=true;
        $groupOptions=$this->courses->optionsConverting($groupCourses,$with_empty);

        $centerOptions=$this->getCenterOptions();
        $categoryOptions=$this->getCategoryOptions();
        $teacherOptions=$this->teachers->optionsByCenter($center_id);
        
        $termOptions=$this->terms->options();

        return response()->json([
                        
                            'course' => $course,
                            'centerOptions' => $centerOptions,
                            'categoryOptions' => $categoryOptions,
                            'teacherOptions' => $teacherOptions,
                            'termOptions' => $termOptions,
                            'groupOptions' => $groupOptions,
                            'groupCourses' => $groupCourses
                            
                        ]);
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

        if($course) return $this->courses->update($courseValues , $categoryIds, $teacherIds ,$course);

        return $this->courses->store($courseValues,$categoryIds,$teacherIds);
        
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
        $with=['status','center','term','categories','teachers','classTimes'];
        $course = Course::with($with)->findOrFail($id);

        $course->populateViewData();
        
        $course->canEdit=$course->canEditBy($current_user);
        $course->canDelete=$course->canDeleteBy($current_user);
        $course->canReview=$course->canReviewBy($current_user);

        $course->getParentCourse();
        
        return response()->json(['course' => $course]);
    }
    public function edit($id)
    {
        $course = Course::with(['categories','teachers'])->findOrFail($id);     
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        //$course->canReview=$course->canReviewBy($current_user);

        $course->begin_date=Helper::checkDateString($course->begin_date);
        $course->end_date=Helper::checkDateString($course->end_date);

        $course->isCredit();
        

        if($course->isGroup()) return $this->editGroupCourse($course);

        return $this->editCourse($course);   
    }
   
    private function editCourse(Course $course)
    {
        $course->group=0;
        
        $categoryOptions=$this->getCategoryOptions();
        $centerOptions=$this->getCenterOptions();    
        $termOptions=$this->terms->options();
        $teacherOptions=$this->teachers->optionsByCenter($course->center_id);
       
        
        foreach ($course->teachers as $teacher) {
            $teacher->name=$teacher->getName();
        }
               
        return response()->json([
                                    'course' => $course,
                                    'centerOptions' => $centerOptions,
                                    'categoryOptions' => $categoryOptions,
                                    'teacherOptions' => $teacherOptions,
                                    'termOptions' => $termOptions,
                                ]);    
    }
    private function editGroupCourse(Course $course)
    {
        $course->group=1;
        
        $centerOptions=$this->getCenterOptions();    
        $categoryOptions=$this->getCategoryOptions();
        $teacherOptions=$this->teachers->optionsByCenter($course->center_id);
        $termOptions=$this->terms->options();

        
        $groupCourses= $this->courses->getGroupCourses($course->term_id,$course->center_id)->get();
        if(!Helper::isNullOrEmpty($groupCourses)){
            $groupCourses=$groupCourses->filter(function ($item) use($course) {
                return $item->id != $course->id;
            })->all();
        }
        
        $with_empty=true;
        $groupOptions=[];
        if(count($groupCourses)){
            $groupOptions=$this->courses->optionsConverting($groupCourses,$with_empty);
        } 
        
        foreach ($course->teachers as $teacher) {
            $teacher->name=$teacher->getName();
        }
               
        return response()->json([
                                    'course' => $course,
                                    'centerOptions' => $centerOptions,
                                    'categoryOptions' => $categoryOptions,
                                    'teacherOptions' => $teacherOptions,
                                    'termOptions' => $termOptions,
                                    'groupOptions' => $groupOptions,
                                    'groupCourses' => $groupCourses
                                ]);    
    }
    public function update(CourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);     
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();         
        }
        $number=$request->getCourseNumber();
        if($number){
            $number_exist=$this->courses->numberExist($number,$id);
            if($number_exist) return response()->json([ 'course.number' => ['課程編號重複了']], 422);
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
    public function groupOptions()
    {
        $request = request();
        $term_id=(int)$request->term; 
        $center_id=(int)$request->center;
       
        $options=[];
        $groupCourses= $this->courses->getGroupCourses($term_id,$center_id)->get();
        
        if(!Helper::isNullOrEmpty($groupCourses)){
            $with_empty=true;
            $options=$this->courses->optionsConverting($groupCourses,$with_empty);
        }

        return response()->json([
                                    'options' => $options ,
                                    'groupCourses' => $groupCourses
                                ]);     
    }
    // private function getGroupOptions($term_id,$center_id)
    // {
    //     $groupCourses= $this->courses->getGroupCourses($term_id,$center_id)
    //                                  ->get();
    //     $with_empty=true;
    //     $options=$this->courses->optionsConverting($groupCourses,$with_empty);

    //     return $options;
    // }

    public function options()
    {
        $options=[];
        
       
        $teacher_id=(int)request()->teacher;
        // $teacher_id=array_key_exists('teacher', $params) ? (int)$params['teacher'] : 0 ;
        if($teacher_id){
            
            $courseList=$this->courses->getByTeacher($teacher_id)->get();
            $options=$this->courses->optionsConverting($courseList);
            return response()  ->json(['options' => $options ]); 
        }     

        

        $term_id= (int)request()->term; 
        $center_id=(int)request()->center;
        $parent_course_id=(int)request()->parent;
        $sub_course_id=(int)request()->sub;

        $parentCourses=$this->courses->parentCourses();

        $params = request()->toArray();                    
        $hasReviewed =array_key_exists('reviewed', $params);
        if($hasReviewed){
            $reviewed=(bool)$params['reviewed'];
            $parentCourses=$parentCourses->where('reviewed',$reviewed);
        };
        
        
        
        $parentCourses=$parentCourses->where('term_id',$term_id)
                                     ->where('center_id',$center_id)
                                     ->orderBy('credit_count')
                                     ->get();
                                     
        $parentOptions=$this->courses->optionsConverting($parentCourses);  

        $subOptions=[];
        $parent_Course=null;
        if($parent_course_id){
            $parent_Course=Course::find($parent_course_id);
            $subCourses=$this->courses->subCourses($parent_course_id)
                                       ->get();
            $subOptions=$this->courses->optionsConverting($subCourses); 
        }
          
        return response()->json([
                                    'parentOptions' => $parentOptions ,
                                    'subOptions' => $subOptions ,
                                    'parentCourse' => $parent_Course 
                               ]);     
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

    public function subCourses()
    {
        $request = request();
        $parent=(int)$request->parent;    
        $courseList=[];
        if($parent){
            $courseList=$this->courses->subCourses($parent)->get();

        }
      

        foreach ($courseList as $course) {
            $course->validSignups=count ($course->validSignups());
             
         }

        return response()
                ->json([
                    'courseList' => $courseList
                ]);  

    }
    
    
    
    
    
    
}
