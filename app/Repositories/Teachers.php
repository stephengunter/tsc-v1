<?php

namespace App\Repositories;

use App\User;
use App\Profile;
use App\Teacher;
use App\Course;
use App\Center;
use App\Role;

use App\Repositories\Users;
use App\Repositories\Centers;

use App\Support\Helper;
use Excel;
use DB;
use App\Events\TeacherCreated;
use App\Events\TeacherDeleted;

use App\Events\UserRegistered;


class Teachers 
{
    public function __construct(Users $users, Centers $centers)                          
    {
        $this->users=$users;  
        $this->centers=$centers; 
        
    }

    public function getRoleName()
    {
        return Role::teacherRoleName();
    }
    public function initialize()
    {
        return Teacher::initialize();
    }
    public function getAll()
    {
         return Teacher::where('removed',false);
    }

    public function getByName($name,$group=false)
    {
        if($group){
            $group_teacher=$this->teacherGroups()->whereHas('user', function($query)use ($name){
                $query->where('name', $name);
            })->first();
            return $group_teacher;
        }

        $ids=$this->getAll()->pluck('user_id')->toArray();
        $user_ids=Profile::whereIn('user_id',$ids)->where('fullname',$name)->pluck('user_id')->toArray();
        
        if(!count($user_ids)) return null;
        return Teacher::whereIn('user_id', $user_ids);
        
    }

    public function unReviewedTeachers()
    {
        return $this->getAll()->where('reviewed',false);
    }
    public function teacherGroups()
    {
         return $this->getAll()->where('group',true);
    }
    public function getByCenter($center_id)
    {
        $teachers=$this->getAll()->whereHas('centers', function($q) use ($center_id)
        {
            $q->where('id',$center_id );
        });
        return $teachers;
    }
    public function getByCenters(array $center_ids)
    {
        $teachers=$this->getAll()->whereHas('centers', function($q) use ($center_ids)
        {
            $q->whereIn('id',$center_ids );
        });
        return $teachers;
    }
    public function findOrFail($id)
    {
        $teacher = Teacher::findOrFail($id);
        return $teacher;
       
    }
    public function getById($id)
    {
        return $this->getAll()->where('user_id',$id)->first();
       
    }
    public function teachersInGroup($group_id)
    {
        $teacherGroup = $this->findOrFail($group_id);

        $teacher_ids=$teacherGroup->teacher_ids;
        $ids= explode( ',', $teacher_ids );

        return $this->getAll()->whereIn('user_id',$ids);
                              
        
       
    }
    
    public function store($user ,$values)
    {
        $teacher=$user->teacher;
        if(!$teacher){
            $teacher=new Teacher($values);  
            $user->teacher()->save($teacher);
        }else{
            $user->teacher->update($values);
        }
        return  $user;
    }

    public function update($values,$id)
    {
         $teacher = Teacher::findOrFail($id);
         $teacher->update($values);

         return $teacher;
    }

    public function updateReview($id,$reviewed,$current_user)
    {
        $teacher = Teacher::findOrFail($id);
        if(!$teacher->canReviewBy($current_user)){
            throw new AuthenticationException();    
        }

        $teacher->reviewed=$reviewed;
        if($reviewed){
            $teacher->reviewed_by=$current_user->id;
        }else{
            $teacher->reviewed_by='';
        }
        
        $teacher->save();
        
       
         
        return $teacher;
    }

    

    public function delete($id,$current_user)
    {
         $teacher = Teacher::findOrFail($id);
         $values=[
            'active' =>0,
            'reviewed' =>0,
            'removed' => 1,
            'updated_by' => $current_user->id
         ];
        
         $teacher->update($values);
         event(new TeacherDeleted($teacher, $current_user));
    }
   

    public function optionsByCenter($center_id,$reviewed=false)
    {
        $teachers=$this->getByCenter($center_id);
        
        if($reviewed){
            $teachers=$teachers->where('reviewed',true);
        }
        
        
        return $this->optionsConverting($teachers->orderBy('group')->get());
       
    }

    public function options($course)
    {
        $course=Course::findOrFail($course);
        return $this->optionsConverting($course->teachers);
       
    }


    public function optionsConverting($teacherList)
    {
        if(Helper::isNullOrEmpty($teacherList)) return [];
        $options=[];
        foreach($teacherList as $teacher)
        {
            $item=[ 'text' => $teacher->getName() , 
                     'value' => $teacher->user_id , 
                 ];
            array_push($options,  $item);
        }

        return $options;
    }
    
    public function attachCenter($teacher_id,$center_id)
    {
            $teacher=$this->findOrFail($teacher_id); 
            $teacher->attachCenter($center_id);
            
            return $teacher;
    }
    public function detachCenter($teacher_id,$center_id)
    {
            $teacher=$this->findOrFail($teacher_id); 
            $teacher->detachCenter($center_id);
            return $teacher;
    }

    public function storeTeacherGroup($name,$description,$center_id,$current_user)
    {
       
        $user= DB::transaction(function () 
            use($name,$description,$current_user)
            {
                $updated_by=$current_user->id;
                $userValues=[
                    'name' => $name,
                    'updated_by' =>$updated_by
                ];
                $profileValues=[
                    'fullname' => $name,
                    'SID' => '',
                    'updated_by'=>$updated_by
                ];
                $teacherValues=[
                    'description' => $description,
                    'group' => 1,
                    'updated_by'=>$updated_by
                ];

                $user=new User($userValues);            
                $user->save();
                $profileValues['SID']=$user->id;
                $profile=new Profile($profileValues);
                $user->profile()->save($profile);

                
                $teacher=new Teacher($teacherValues);  

                
                $user->teacher()->save($teacher);
            
                return $user;
            });

        $teacher= Teacher::findOrFail($user->id);
        $teacher->attachCenter($center_id);

        event(new TeacherCreated($teacher, $current_user));
        
        return    $user->teacher; 
    }

    public function updateTeacherGroup($teacher_id,$name,$description,$center_id,$current_user)
    {
        $user= DB::transaction(function() 
        use($teacher_id,$name,$description,$current_user)
        {
            $updated_by=$current_user->id;
            $userValues=[
                'name' => $name,
                'updated_by' =>$updated_by
            ];
            $profileValues=[
                'fullname' => $name,
                'updated_by'=>$updated_by
            ];
            $teacherValues=[
                'description' => $description,
                'group' => 1,
                'updated_by'=>$updated_by
            ];

            $teacher=Teacher::findOrFail($teacher_id);
            $teacher->update($teacherValues);

            $user=User::findOrFail($teacher_id);
            $user->update($userValues);

            $profile=Profile::findOrFail($teacher_id);
            $profile->update($profileValues);
        
            return $user;
        });

        $teacher= Teacher::findOrFail($user->id);
        $teacher->attachCenter($center_id);

        return    $user->teacher; 
        
    }
   
    public function storeTeacher($userValues,$profileValues,$teacherValues,$user_id,$teacherId,$current_user,$center_id)
    {
        $user= DB::transaction(function() 
            use($userValues,$profileValues,$teacherValues,$user_id,$teacherId)
            {
                $user=null;
                if($user_id){
                    $user=User::findOrFail($user_id);
                    $user->update($userValues);
                    $user->profile->update($profileValues);
                }else{
                    $user=new User($userValues);
                    $user->password= config('app.default_password');
                    $user->save();
                    $profile=new Profile($profileValues);
                    $user->profile()->save($profile);
                }

                
                if($teacherId){
                    $teacher = Teacher::findOrFail($teacherId);
                    $teacher->update($teacherValues);
                }else{
                    $teacher=$user->teacher;
                    if(!$teacher){
                        $teacher=new Teacher($teacherValues);  
                        $user->teacher()->save($teacher);
                    }else{
                        $user->teacher->update($teacherValues);
                    }
                }
            
                return $user;
        });



        $teacher= Teacher::findOrFail($user->id);

        $teacher->attachCenter($center_id);
        
        event(new TeacherCreated($teacher, $current_user));

        $is_new_user=true;
        if($user_id)  $is_new_user=false;

        if($is_new_user){
            event(new UserRegistered($user));            
        }

        return $teacher;
    }

    public function importTeachers($file,$current_user)
    {
        $err_msg='';
        
        
        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();

        $teacherList=$excel->toArray()[0];
        for($i = 1; $i < count($teacherList); ++$i) {
            $row=$teacherList[$i];

            $fullname=trim($row['fullname']);
            if(!$fullname){
               continue;
            }

            $center_code=trim($row['center']);
            
            if(!$center_code){
                $err_msg .= '中心代碼不可空白' . ',';
                continue;
            }
            $center=$this->centers->getByCode($center_code);
            if(!$center) {
                $err_msg .= '中心代碼' .$center_code . '錯誤'. ',';
                continue;
            }

            $exist_user=null;
            $sid=trim($row['id']);
            if($sid){
                $exist_user=$this->users->findBySID(strtoupper($sid));
               
            }

            $gender=(int)trim($row['gender']);
            if($gender) $gender=true;
            else $gender=false;

            $dob=trim($row['dob']);
            if($dob){
                $pieces=explode('/', $dob);
                $year = (int)$pieces[0] + 1911;
                $dob= $year . '/'.$pieces[1]. '/'.$pieces[2];
                
            }

            $phone=trim($row['phone']);
            $email=trim($row['email']);

            if(!$exist_user){
                $userList=$this->users->findUsers($email, $phone);
                if(count($userList)) $exist_user=$userList[0];
            }

            $education=trim($row['education']);
            $specialty=trim($row['specialty']);
            $job=trim($row['job']);
            $description=trim($row['description']);
            
            $experiences='';               
            $array_experiences = explode(',', trim($row['experiences']));
            for($j = 0; $j < count($array_experiences); ++$j){
                $experiences .= $array_experiences[$j] . '<br>';
            }


            $updated_by=$current_user->id;
    
            $teacherValues=[
                'education' => $education,
                'specialty' => $specialty,
                'job' => $job,
                
                'description' => $description,
                'experiences' => $experiences,
                
                'updated_by' => $updated_by,
                'removed' => false
            ];
            $userValues=[
                'name' => $fullname,
                'email' => $email,
                'phone' => $phone,
                'updated_by' => $updated_by,
                'removed' => false
            ];
            $profileValues=[
                'fullname' => $fullname,
                'SID' => $sid,
                'gender' => $gender,
                'dob' => $dob,               
                'updated_by' => $updated_by,
              
            ];
            
            $user_id=0;
            if($exist_user) $user_id=$exist_user->getUserId();

            $teacher_id=0;
            if($exist_user){
                if($exist_user->teacher) $teacher_id=$exist_user->getUserId();
            }

            $teacher=$this->storeTeacher($userValues,$profileValues,$teacherValues,
            $user_id,$teacher_id,$current_user,$center->id);

            
            if(!$teacher) continue;


            $zipcode=trim($row['zipcode']);
            $street=trim($row['street']);

            if($zipcode){
                $teacher->user->updateAddress($zipcode, $street,$updated_by);
            }
        }

        return $err_msg;
    }
    public function importTeacherGroups($file,$current_user)
    {
        $err_msg='';        
        
        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();

        $teacherList=$excel->toArray()[0];
        for($i = 1; $i < count($teacherList); ++$i) {
            $row=$teacherList[$i];
            
            $name=trim($row['name']);
            if(!$name)  continue;


            $group=true;  
            $center_code=trim($row['center']);           
            
            if(!$center_code){
                $err_msg .= '中心代碼不可空白' . ',';
                continue;
            }
            $center=$this->centers->getByCode($center_code);
            if(!$center) {
                $err_msg .= '中心代碼' .$center_code . '錯誤'. ',';
                continue;
            }

            $description=trim($row['description']);

            $exist_teacher_group=null;
            $id=(int)trim($row['id']);
            if($id){
                $exist_teacher_group=Teacher::find($id);
            }

            if($exist_teacher_group){
                $teacher=$this->updateTeacherGroup($id,$name,$description,$center->id,$current_user);
            }else{
                $teacher=$this->storeTeacherGroup($name,$description,$center->id,$current_user);                
            }

            
        }  //end for  

        return $err_msg;
        
    }
    
}