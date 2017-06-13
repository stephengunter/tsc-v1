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
         $roleName='Owner';
         $user=User::where('email','traders.com.tw@gmail.com')->firstOrFail();

         $role=Role::where('name',$roleName)->firstOrFail();
         $user->attachRole($role);

         $adminValues = [
		     'role' => $roleName,	
             'user_id' => $user->id,
		 ];
         Admin::create($adminValues);
        

         $admin=Admin::find($user->id);

         $centerIds=Center::all()->pluck('id')->toArray();
         for($i=0; $i< count($centerIds); $i++){
             $admin->centers()->attach($centerIds[$i]);
         }
        
    }
}
