<?php

use Illuminate\Database\Seeder;

use App\Schedule;
use App\Course;

class ScheduleSeeder extends Seeder
{
    public function run()
    {
       $i=1;
       $course=Course::where('name','西班牙語初階班')->first();
       $schedule=Schedule::create([
           'course_id' => $course->id,
			'order' => $i,
			'title' => '課程簡介',
			'content' => '',
            'materials' => ''
		]);
        $i++;
         $schedule=Schedule::create([
           'course_id' => $course->id,
			'order' => $i,
			'title' => '咖啡生豆農藝技術系統工程',
			'content' => '採收標準與漿果處理技術',
            'materials' => ''
		]);
       
         $i++;
        $schedule=Schedule::create([
           'course_id' => $course->id,
			'order' => $i,
			'title' => '烘焙品質',
			'content' => '了解烘焙技術生豆水分控制',
            'materials' => ''
		]);
        

         $i++;
        $schedule=Schedule::create([
           'course_id' => $course->id,
			'order' => $i,
			'title' => '咖啡飲料風味',
			'content' => '沖煮控制技術與樣品試喝',
            'materials' => '咖啡壺'
		]);


    }
}
