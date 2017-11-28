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
			'name' => '莉玲',
			'email' =>'liling@mail.tcu.edu.tw',
			'phone' => '0912148533',
			'password' => '000000',
			'email_confirmed' => true,
			'remember_token' => str_random(10),
		]);
		$profile=new \App\Profile([
			'user_id' => $user->id,					
			'fullname'=> '蕭莉玲',
			'dob' =>'1981-02-27',
			'gender' => 0,
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



        $user=\App\User::create([
			'name' => 'wisekind',
			'email' =>'wisekind@gms.tcu.edu.tw',
			'phone' => '0921565879',
			'password' => '000000',
			'email_confirmed' => true,
			'remember_token' => str_random(10),
		]);
		$profile=new \App\Profile([
			'user_id' => $user->id,					
			'fullname'=> '郭芝穎',
			'dob' =>'1979-12-13',
			'gender' => 0,
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
