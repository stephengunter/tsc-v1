<?php
use App\Category;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    public function run() {
		$categories = [
			[
				'name' => '社會科學',				
				'active' => 1,
                'icon' =>'fa fa-meetup'
			],
			[
				'name' => '人文藝術',				
				'active' => 1,
                'icon' =>'fa fa-ravelry'
			],
			[
				'name' => '古典音樂',
                'icon' =>'fa fa-music',
				'active' => 1,
			],
			[
				'name' => '學分班',
                'icon' =>'fa fa-graduation-cap',
				'active' => 1,
			],
			[
				'name' => '親子班',
                'icon' =>'fa fa-child',
				'active' => 1,
			],
			[
				'name' => '最新課程',
                'icon' =>'',
				'active' => 1,
				'order' => 3,
				'public' => true,
			],
			[
				'name' => '推薦課程',
                'icon' =>'',
				'active' => 1,
				'order' => 5,
				'public' => true,
			],
			
		];

		foreach ($categories as $key => $value) {
			Category::create($value);
		}

		
	}
}
