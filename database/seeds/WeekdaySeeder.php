
<?php

use App\Weekday;
use Illuminate\Database\Seeder;

class WeekdaySeeder extends Seeder 
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		 $days = [
			[
				'name' => 'Sun',
				'text' => '星期日',
				'val'=> 0

			],
			[
				'name' => 'Mon',
				'text' => '星期一',
				'val'=> 1
			],
			[
				'name' => 'Tue',
				'text' => '星期二',
				'val'=> 2
			],
			[
				'name' => 'Wed',
				'text' => '星期三',
				'val'=> 3
			],
            [
				'name' => 'Thu',
				'text' => '星期四',
				'val'=> 4
			],
            [
				'name' => 'Fri',
				'text' => '星期五',
				'val'=> 5
			],
            [
				'name' => 'Sat',
				'text' => '星期六',
				'val'=> 6
			],

		];

		foreach ($days as $key => $value) {
			Weekday::create($value);
		}
	}
}
	
