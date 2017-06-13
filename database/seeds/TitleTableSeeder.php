
<?php

use App\Title;
use Illuminate\Database\Seeder;

class TitleTableSeeder extends Seeder 
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		 $titles = [
			[
				'name' => '教師',
			],
            [
				'name' => '師兄',
			],
            [
				'name' => '師姐',
			],

		];
  
		foreach ($titles as $key => $value) {
			Title::create($value);
		}
	}
}
	
