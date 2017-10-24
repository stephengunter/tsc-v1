<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
		
		$this->call(LeaveSeeder::class);
		 $this->call(NoticesSeeder::class);
		 $this->call(TermTableSeeder::class);
		 $this->call(AccountSeeder::class);
		 $this->call(WeekdaySeeder::class);
		 $this->call(TitleTableSeeder::class);
		 $this->call(IdentitiesSeeder::class);

		 $this->call(CityDistrictSeeder::class);


		  $this->call(DiscountSeeder::class);

		 $this->call(PermissionTableSeeder::class);
   		 $this->call(RoleTableSeeder::class);

		 $this->call(CenterTableSeeder::class);
		 
    	 $this->call(HolidaySeeder::class);
		 
		 $this->call(ClassroomSeeder::class);
		 
		 $this->call(CategoryTableSeeder::class);

		 $this->call(UserSeeder::class);

		//  $this->call(TeacherSeeder::class);

		//  $this->call(CourseSeeder::class);
		//  $this->call(ScheduleSeeder::class);
		//  $this->call(AdminSeeder::class);

		 
		// $this->call(SignupSeeder::class);
		


			
	}
}
