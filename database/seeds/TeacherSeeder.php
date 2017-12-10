<?php

use Illuminate\Database\Seeder;
use App\Repositories\Teachers;

use Illuminate\Http\File;

class TeacherSeeder extends Seeder
{
    public function __construct(Teachers $teachers)                               
    {
        
        $this->teachers=$teachers;
         
	}
    public function run()
    {
        $dev=config('app.dev');
		$email=$dev['email'];

		$current_user=\App\User::where('email',$email)->first();


		$path='import/teachers.xlsx';

        $file=new File(storage_path($path));

        $this->teachers->importTeachers($file,$current_user);
    }
}
