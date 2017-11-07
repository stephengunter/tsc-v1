<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;
use App\Admin;
use App\Center;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user=\App\User::create([
			'name' => '花組長',
			'email' =>'test@tcust.com.tw',
			'phone' => '0932000000',
			'password' => '000000',
			'email_confirmed' => true,
			'remember_token' => str_random(10),
		]);
		$profile=new \App\Profile([
			'user_id' => $user->id,					
			'fullname'=> '花組長',
			'dob' =>'1970-1-1',
			'gender' => 1,
		]);
        $user->profile()->save($profile); 
        
        $roleName=Role::ownerRoleName();
		$user->addRole($roleName);

        $adminValues = [
		     'role' => $roleName,	
             'user_id' => $user->id,
		 ];
        Admin::create($adminValues);

        $center=Center::where('head',true)->first();
        $admin=Admin::find($user->id);
        $admin->attachCenter($center->id);

        
        
		
        

         
        
    }
}
