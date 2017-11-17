
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
		$path='app/public/contents/';
		$contents = [
			[
				'key' => 'about',
				'title'=> '單位理念',
				'text'=> File::get(storage_path($path . 'about.target.html')),
				'active' => 1,
				'order' => 30,
                
			],
			[
				'key' => 'about',
				'title'=> '校本部簡介',
				'text'=> File::get(storage_path($path . 'about.intro.html')),
				'active' => 1,
				'order' => 25,
                
			],
			[
				'key' => 'about',
				'title'=> '設置辦法',
				'text'=> File::get(storage_path($path . 'about.law.html')),
				'active' => 1,
				'order' => 22,
                
			],

			[
				'key' => 'faq',
				'title'=> '報名須知',
				'text'=> File::get(storage_path($path . 'faq.must_know.html')),
				'active' => 1,
				'order' => 30,
                
			],
			
           

		];
  
		foreach ($contents as $key => $value) {
			Content::create($value);
		}
	}
}
	
