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
			'code' => 'TPE',
			'active' => 1,
		]);

		$contactinfo=ContactInfo::where('tel','03-8679080')->first();
		$center=Center::create([
			'name' => '花蓮中心',
			'contact_info' => $contactinfo->id,
			'code' => 'HUA',
			'active' => 1,
		]);

		$contactinfo=ContactInfo::where('tel','03-9382001')->first();
		$center=Center::create([
			'name' => '宜蘭中心',
			'contact_info' => $contactinfo->id,
			'code' => 'ILA',
			'active' => 1,
		]);

		$contactinfo=ContactInfo::where('tel','04-2278909')->first();
		$center=Center::create([
			'name' => '台中中心',
			'contact_info' => $contactinfo->id,
			'code' => 'TCU',
			'active' => 1,
		]);

		
	}
}
