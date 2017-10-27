<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	private function addDev(){
		$user=\App\User::create([
			'name' => 'stephen',
			'email' =>'traders.com.tw@gmail.com',
			'phone' => '0936060049',
			'password' => 'secret',
			'email_confirmed' => true,
			'remember_token' => str_random(10),
		]);
		$profile=new \App\Profile([
			'user_id' => $user->id,					
			'fullname'=> '何金水',
			'dob' =>'1979-3-12',
			'gender' => 1,
		]);
		$user->profile()->save($profile);

		$roleName='Dev';
		$user->addRole($roleName);
	}

	public function run() {

		$this->call(WeekdaySeeder::class);
		$this->call(CityDistrictSeeder::class);

		$this->call(RoleTableSeeder::class);
		$this->addDev();

		
		$this->call(TitleTableSeeder::class);
		$this->call(TermTableSeeder::class);		
		$this->call(CenterTableSeeder::class);

		$this->call(AdminSeeder::class);

		
		 //$this->call(LeaveSeeder::class);
		 
		 
		//$this->call(AccountSeeder::class);
		 
		
		// $this->call(IdentitiesSeeder::class);

		 


		  //$this->call(DiscountSeeder::class);

		 //$this->call(PermissionTableSeeder::class);
   		 

		 
		 
    	 //$this->call(HolidaySeeder::class);
		 
		// $this->call(ClassroomSeeder::class);
		 
		 //this->call(CategoryTableSeeder::class);

		// $this->call(UserSeeder::class);

		 

		//  $this->call(TeacherSeeder::class);

		//  $this->call(CourseSeeder::class);
		//  $this->call(ScheduleSeeder::class);
		//  $this->call(AdminSeeder::class);

		 
		// $this->call(SignupSeeder::class);

		//$this->call(NoticesSeeder::class);
		


			
	}
}
