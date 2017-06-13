
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
				'year' => 105,
				'order'=> 1,
				'name' => '105年上',
				'number' => 1051,
			],
            [
				'year' => 105,
				'order'=> 2,
				'name' => '105年中',
				'number' => 1052,
			],
            [
				'year' => 105,
				'order'=> 3,
				'name' => '105年下',
				'number' => 1053,
			],
            [
				'year' => 106,
				'order'=> 1,
				'name' => '106年上',
				'number' => 1061,
				'active'=>true
			],

		];
  
		foreach ($terms as $key => $value) {
			Term::create($value);
		}
	}
}
	
