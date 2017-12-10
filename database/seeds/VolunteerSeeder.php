<?php

use Illuminate\Database\Seeder;
use App\Repositories\Volunteers;

use Illuminate\Http\File;

class VolunteerSeeder extends Seeder
{
   public function __construct(Volunteers $volunteers)                               
   {
      
      $this->volunteers=$volunteers;
      
   }
   public function run()
   {
      $dev=config('app.dev');
      $email=$dev['email'];

      $current_user=\App\User::where('email',$email)->first();


      $path='import/volunteers.xlsx';

      $file=new File(storage_path($path));

      $this->volunteers->importVolunteers($file,$current_user);
   }
}
