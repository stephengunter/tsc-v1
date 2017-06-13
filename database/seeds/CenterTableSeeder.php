<?php
use App\Center;
use App\ContactInfo;
use Illuminate\Database\Seeder;

class CenterTableSeeder extends Seeder
{
    public function run() {

		$contactinfo=ContactInfo::where('tel','02-23829001')->first();

		$center=Center::create([
			'name' => '台北中心',
			'contact_info' => $contactinfo->id,
			'active' => 1,
		]);

		$contactinfo=ContactInfo::where('tel','03-8679080')->first();
		$center=Center::create([
			'name' => '花蓮中心',
			'contact_info' => $contactinfo->id,
			'active' => 1,
		]);

		$contactinfo=ContactInfo::where('tel','03-9382001')->first();
		$center=Center::create([
			'name' => '宜蘭中心',
			'contact_info' => $contactinfo->id,
			'active' => 1,
		]);

		$contactinfo=ContactInfo::where('tel','04-2278909')->first();
		$center=Center::create([
			'name' => '台中中心',
			'contact_info' => $contactinfo->id,
			'active' => 1,
		]);

		// $centers = [
		// 	[
		// 		'name' => '台北中心',
		// 		'contact_info' => $contact_infos[0]->id,
		// 		'active' => 1,
		// 	],
		// 	[
		// 		'name' => '花蓮中心',
		// 		'contact_info' => $contact_infos[1]->id,
		// 		'active' => 1,
		// 	],
		// 	[
		// 		'name' => '高雄中心',
		// 		'contact_info' => $contact_infos[2]->id,
		// 		'active' => 1,
		// 	],
		// 	[
		// 		'name' => '新竹中心',
		// 		'contact_info' => $contact_infos[3]->id,
		// 		'active' => 1,
		// 	],
			
		// ];

		// foreach ($centers as $key => $value) {
		// 	Center::create($value);
		// }

		
	}
}
