
<?php

use App\Notice;
use Illuminate\Database\Seeder;

class NoticesSeeder extends Seeder 
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		 $items = [
			[
				'title' => '106年9月15日 因颱風來襲停止上課',
                'content'=>'梅姬颱風來襲，106年9月15日全天停止上課。當日原訂課程順延一週。',
                'public'=>1,
                'active'=>1,
                'date'=>'2017-9-14'

			],
            [
				'title' => '106年下半年度最新課程已上線 歡迎您的加入',
                'content'=>'',
                'public'=>1,
                'active'=>1,
                'date'=>'2017-8-5'
			],
            
           

		];
  
		foreach ($items as $key => $value) {
			Notice::create($value);
		}
	}
}
	
