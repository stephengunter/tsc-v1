<?php

use Illuminate\Database\Seeder;
use App\Status;
use App\Center;
use App\Term;
use App\Course;
use App\Category;
use App\Teacher;
use App\ClassTime;
use App\Weekday;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $term=Term::where('number',1061)->first();
        $center=Center::where('name','台北中心')->first();
       
        $course=Course::create([
           'name' => '西班牙語初階班',
           'term_id' => $term->id,
           'center_id' => $center->id,
            'number' => '1061003',
           'begin_date' => '2017-7-16',
           'end_date' => '2017-9-30',
           'weeks' => '12',
           'hours' => '24',

           'credit_count' => 3,
           
           'net_signup' => true,


           'tuition' => 3000,
           'cost' => 1200,
            'materials' => '原文書,光碟',
            'open_date' => '2017-4-16',
            'close_date' => '2017-6-15',
            'limit' => 30,
            'min' => 10,
            'active' => true,
        ]);

        $statusValues=Status::initialize($course);
        $status=new Status($statusValues);
        $course->status()->save($status);

        $category=Category::where('name','社會科學')->first();
        $course->categories()->save($category);

        $category=Category::where('name','推薦課程')->first();
         $course->categories()->save($category);

       $teacher=Teacher::where('specialty','西班牙語')->first();
       $course->teachers()->save($teacher);

       $weekday=Weekday::where('name','Sat')->first();
       $classtime=new ClassTime([
            'weekday_id' => $weekday->id,
            'on'=>1600,
            'off'=>1800
       ]);
       $course->classTimes()->save($classtime);

     
        $course=Course::create([
           'name' => '咖啡鑑賞班',
            'term_id' => $term->id,
           'center_id' => $center->id,
            'number' => '1061007',

            
          
           'net_signup' => true,

           'begin_date' => '2016-4-22',
           'end_date' => '2016-6-2',
           'weeks' => '6',
           'hours' => '9',
           'tuition' => 2600,
           'cost' => 0,
            'materials' => '',
            'open_date' => '2016-3-10',
            'close_date' => '2016-4-8',
            'limit' => 25,
            'min' => 10,
            'active' => true,
        ]);

        $statusValues=Status::initialize($course);
        $status=new Status($statusValues);
        $course->status()->save($status);

        $category=Category::where('name','最新課程')->first();
        $course->categories()->save($category);
         $category=Category::where('name','人文藝術')->first();
        $course->categories()->save($category);

        $teacher=Teacher::where('specialty','有機農業')->first();
        $course->teachers()->save($teacher);
         $teacher=Teacher::where('specialty','中醫')->first();
        $course->teachers()->save($teacher);

          $weekday=Weekday::where('name','Fri')->first();
         $classtime=new ClassTime([
            'weekday_id' => $weekday->id,
            'on'=>1830,
            'off'=>2000
       ]);
       $course->classTimes()->save($classtime);

       $weekday=Weekday::where('name','Mon')->first();
         $classtime=new ClassTime([
            'weekday_id' => $weekday->id,
            'on'=>1830,
            'off'=>2000
       ]);
       $course->classTimes()->save($classtime);
         

        $term=Term::where('number',1052)->first();
        $center=Center::where('name','花蓮中心')->first();
       
        $course=Course::create([
           'name' => '家庭水電班',
            'term_id' => $term->id,
           'center_id' => $center->id,
            'number' => '1052019',

           
           
           'net_signup' => true,

           'begin_date' => '2016-6-5',
           'end_date' => '2016-9-19',
           'weeks' => '15',
           'hours' => '30',
           'tuition' => 7200,
           'cost' => 1800,
            'materials' => '電表,電鑽',
            'open_date' => '2016-4-6',
            'close_date' => '2016-4-30',
            'limit' => 30,
            'min' => 10,
            'active' => true,
        ]);

        $statusValues=Status::initialize($course);
        $status=new Status($statusValues);
        $course->status()->save($status);

        $category=Category::where('name','社會科學')->first();
        $course->categories()->save($category);

        $teacher=Teacher::where('specialty','水電工程')->first();
        $course->teachers()->save($teacher);

        $weekday=Weekday::where('name','Sat')->first();
         $classtime=new ClassTime([
            'weekday_id' => $weekday->id,
            'on'=>1400,
            'off'=>1600
       ]);
       $course->classTimes()->save($classtime);
     
     
       

    }
}
