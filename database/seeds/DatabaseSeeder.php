<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	private function addDev(){
		$dev=config('app.dev');
		$name=$dev['name'];
		$email=$dev['email'];
		$phone=$dev['phone'];
		$user=\App\User::create([
			'name' => $name,
			'email' =>$email,
			'phone' => $phone,
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

		$this->call(IdentitiesSeeder::class);

		
		$this->call(TermTableSeeder::class);		
		$this->call(CenterTableSeeder::class);

		$this->call(AdminSeeder::class);

		$this->call(ContentSeeder::class);

		$this->call(DiscountSeeder::class);

		$this->call(UserSeeder::class);

		$this->call(CategoryTableSeeder::class);

		$this->call(TeacherSeeder::class);

		$this->call(VolunteerSeeder::class);

		$this->call(CourseSeeder::class);

		$this->call(ClassTimeSeeder::class);
		
		//$this->call(DownloadSeeder::class);

		
		 //$this->call(LeaveSeeder::class);
		 
		 
		//$this->call(AccountSeeder::class);
		 
		
		

		 


		  

		 //$this->call(PermissionTableSeeder::class);
   		 

		 
		 
    	 //$this->call(HolidaySeeder::class);
		 
		// $this->call(ClassroomSeeder::class);
		 
		

		

		 

		

	
		//  $this->call(ScheduleSeeder::class);
		//  $this->call(AdminSeeder::class);

		 
		// $this->call(SignupSeeder::class);

		//$this->call(NoticesSeeder::class);
		


			
	}
}
