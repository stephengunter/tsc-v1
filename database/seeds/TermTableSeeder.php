
<?php

use App\Term;
use Illuminate\Database\Seeder;

class TermTableSeeder extends Seeder 
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		 $terms = [
			
			[
				'year' => 106,
				'order'=> 4,
				'name' => '1064',
				'number' => 1064,
				'open_date' => '2017-9-1',
				'close_date' => '2017-10-31',
				'updated_by' =>51,
				'active'=>true
			],
			[
				'year' => 107,
				'order'=> 1 ,
				'name' => '1071',
				'number' => 1071,
				'open_date' => '2018-1-1',
				'close_date' => '2018-2-28',
				'updated_by' =>51,
				'active'=>true
			],

		];
  
		foreach ($terms as $key => $value) {
			Term::create($value);
		}
	}
}
	
