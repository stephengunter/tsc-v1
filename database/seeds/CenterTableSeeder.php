<?php

use App\Address;
use App\Center;
use App\ContactInfo;
use Illuminate\Database\Seeder;

class CenterTableSeeder extends Seeder
{
	public function run() 
	{
		//花蓮中心
		$address=Address::create([
			'city_id' => 19,
			'district_id' => 345,
			'zipcode' => '',
			'streetAddress' => '中山路352號'
		]);
		$contactinfo=ContactInfo::create([
			'tel' => '03-845236',
			'fax' => '03-845239',
			'contactAddress' => $address->id,
		]);
		$center=Center::create([
			'name' => '花蓮中心',
			'head' => 1,
			'contact_info' => $contactinfo->id,
			'code' => 'HWA',
			'active' => 1,
		]);
		//台北中心
		$address=Address::create([
			'city_id' => 1,
			'district_id' => 12,
			'zipcode' => '',
			'streetAddress' => '木柵路一段32號'
		]);
		$contactinfo=ContactInfo::create([
			'tel' => '02-23829001',
			'fax' => '02-23829019',
			'contactAddress' => $address->id,
		]);
		$center=Center::create([
			'name' => '台北中心',
			'contact_info' => $contactinfo->id,
			'code' => 'TPE',
			'active' => 1,
		]);

		//台南中心
		$address=Address::create([
			'city_id' => 14,
			'district_id' => 218,
			'zipcode' => '',
			'streetAddress' => '成功路153號'
		]);
		$contactinfo=ContactInfo::create([
			'tel' => '06-829001',
			'fax' => '06-829001',
			'contactAddress' => $address->id,
		]);
		$center=Center::create([
			'name' => '台南中心',
			'contact_info' => $contactinfo->id,
			'code' => 'TNN',
			'active' => 1,
		]);

		//桃園中心
		$address=Address::create([
			'city_id' => 7,
			'district_id' => 83,
			'zipcode' => '',
			'streetAddress' => '中華路99號',
		]);
		$contactinfo=ContactInfo::create([
			'tel' => '03-2382900',
			'fax' => '03-2382901',
			'contactAddress' => $address->id,
		]);
		$center=Center::create([
			'name' => '桃園中心',
			'contact_info' => $contactinfo->id,
			'code' => 'TYU',
			'active' => 1,
		]);

		

		
	}
}
