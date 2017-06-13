<?php

use Illuminate\Database\Seeder;
use App\Address;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $addressList = [
			[
				'city_id' => 1,
                'district_id' => 12,
				'zipcode' => '',
				'streetAddress' => '木柵路一段32號',
			],
			[
				'city_id' => 7,
                'district_id' => 77,
				'zipcode' => '',
				'streetAddress' => '中華路99號',
			],
			[
				'city_id' => 10,
                'district_id' => 146,
				'zipcode' => '',
				'streetAddress' => '田尾路116號',
			],
			[
				'city_id' => 9,
                'district_id' => 114,
				'zipcode' => '',
				'streetAddress' => '向上路66號',
			],
			[
				'city_id' => 4,
                'district_id' => 54,
				'zipcode' => '',
				'streetAddress' => '前進路125號',
			],
			[
				'city_id' => 11,
                'district_id' => 167,
				'zipcode' => '',
				'streetAddress' => '中山路352號',
			],
			
		];

		foreach ($addressList as $key => $value) {
			Address::create($value);
		}
    }
}
