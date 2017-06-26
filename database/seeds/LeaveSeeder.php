
<?php

use App\LeaveType;
use Illuminate\Database\Seeder;

class LeaveSeeder extends Seeder 
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		 $types = [
			[
				'name' => '病假',
			],
            [
				'name' => '事假',
			],
            [
				'name' => '產假',
			],
            [
				'name' => '婚假',
			],
           

		];
  
		foreach ($types as $key => $value) {
			LeaveType::create($value);
		}
	}
}
	
