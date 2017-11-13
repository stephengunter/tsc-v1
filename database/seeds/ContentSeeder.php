
<?php

use App\Content;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder 
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		 $contents = [
			[
				'key' => 'about',
				'title'=> '單位理念',
				'text'=> '',
				'active' => 1,
				'order' => 30,
                
			],
			[
				'key' => 'about',
				'title'=> '校本部簡介',
				'text'=> '',
				'active' => 1,
				'order' => 25,
                
			],
			[
				'key' => 'about',
				'title'=> '設置辦法',
				'text'=> '',
				'active' => 1,
				'order' => 22,
                
			],
           

		];
  
		foreach ($contents as $key => $value) {
			Content::create($value);
		}
	}
}
	
