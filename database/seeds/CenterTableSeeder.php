<?php

use App\Address;
use App\Center;
use App\ContactInfo;

use App\Repositories\Centers;
use Illuminate\Database\Seeder;

class CenterTableSeeder extends Seeder
{
	public function __construct(Centers $centers)
    {

        $this->centers=$centers;
       
	} 

	private function import()
	{
		$path='import/';
        $file_name='centers.xlsx';
        
		$file_path=storage_path($path . $file_name);

		$currentUser=\App\User::where('email','traders.com.tw@gmail.com')->first();
		
		$this->centers->importCenters($file_path,$currentUser);
	}
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
			'area_id' => 1,
			'head' => 1,
			'contact_info' => $contactinfo->id,
			'code' => 'A',
			'course_tel' => '03-845236 轉1703或1704',
			'active' => 1,
			'rule'=>'※完成報名繳費後，因故退學者，依下列標準退費：
			<ul>
				 <li>
					  開課前申請退班者，退還已繳學費九折。
				 </li>
				 <li>
					  開課後未逾全期三分之一申請退班者，退還已繳學費半數。
				 </li>
				 <li>
					  在班時間已逾全期三分之一者，將不予退還。
				 </li>
			</ul>
		 ※本中心一律以匯款方式退費，不提供現金退費。'
		]);
		// //台北中心
		// $address=Address::create([
		// 	'city_id' => 1,
		// 	'district_id' => 12,
		// 	'zipcode' => '',
		// 	'streetAddress' => '木柵路一段32號'
		// ]);
		// $contactinfo=ContactInfo::create([
		// 	'tel' => '02-23829001',
		// 	'fax' => '02-23829019',
		// 	'contactAddress' => $address->id,
		// ]);
		// $center=Center::create([
		// 	'name' => '台北中心',
		// 	'contact_info' => $contactinfo->id,
		// 	'code' => 'TPE',
		// 	'active' => 1,
		// ]);

		// //台南中心
		// $address=Address::create([
		// 	'city_id' => 14,
		// 	'district_id' => 218,
		// 	'zipcode' => '',
		// 	'streetAddress' => '成功路153號'
		// ]);
		// $contactinfo=ContactInfo::create([
		// 	'tel' => '06-829001',
		// 	'fax' => '06-829001',
		// 	'contactAddress' => $address->id,
		// ]);
		// $center=Center::create([
		// 	'name' => '台南中心',
		// 	'contact_info' => $contactinfo->id,
		// 	'code' => 'TNN',
		// 	'active' => 1,
		// ]);

		// //桃園中心
		// $address=Address::create([
		// 	'city_id' => 7,
		// 	'district_id' => 83,
		// 	'zipcode' => '',
		// 	'streetAddress' => '中華路99號',
		// ]);
		// $contactinfo=ContactInfo::create([
		// 	'tel' => '03-2382900',
		// 	'fax' => '03-2382901',
		// 	'contactAddress' => $address->id,
		// ]);
		// $center=Center::create([
		// 	'name' => '桃園中心',
		// 	'contact_info' => $contactinfo->id,
		// 	'code' => 'TYU',
		// 	'active' => 1,
		// ]);

		

		
	}
}
