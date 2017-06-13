
<?php

use App\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder 
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		 $holidays = [
             [
				'name' => '春節假期',
                'date' => '2017-1-29',
			],
            [
				'name' => '春節假期',
                'date' => '2017-1-30',
			],
            [
				'name' => '春節假期',
                'date' => '2017-1-31',
			],
			[
				'name' => '春假',
                'date' => '2017-4-3',
			],
            [
				'name' => '春假',
                'date' => '2017-4-4',
			],
           

		];
  
		foreach ($holidays as $key => $value) {
			Holiday::create($value);
		}
	}
}
	
