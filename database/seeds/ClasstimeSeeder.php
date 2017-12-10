<?php

use Illuminate\Database\Seeder;
use App\Repositories\Classtimes;

use Illuminate\Http\File;

class ClassTimeSeeder extends Seeder
{
   public function __construct(Classtimes $classtimes)                               
   {
      
      $this->classtimes=$classtimes;
      
   }
   public function run()
   {
      $dev=config('app.dev');
      $email=$dev['email'];

      $current_user=\App\User::where('email',$email)->first();


      $path='import/classtimes.xlsx';

      $file=new File(storage_path($path));

      $this->classtimes->importAll($file,$current_user);
   }
}
