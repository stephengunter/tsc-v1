<?php

use App\Services\Course\CourseService;
use Illuminate\Database\Seeder;


use Illuminate\Http\File;

class CourseSeeder extends Seeder
{
    public function __construct(CourseService $courseService)                               
    {
        $this->courseService=$courseService;
    }
    public function run()
    {
        
        $dev=config('app.dev');
		$email=$dev['email'];

		$current_user=\App\User::where('email',$email)->first();


		$path='import/courses.xlsx';

        $file=new File(storage_path($path));

        $this->courseService->importCourses($file,$current_user);


        $path='import/course-infoes.xlsx';
        
        $file=new File(storage_path($path));

        $this->courseService->importCourseInfoes($file,$current_user);

    }
}
