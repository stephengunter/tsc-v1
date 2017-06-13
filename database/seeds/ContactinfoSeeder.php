<?php

use Illuminate\Database\Seeder;
use App\ContactInfo;
use App\Address;

class ContactinfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $addressList=Address::take(6)->get();

       $infoList = [
			[
				'tel' => '02-23829001',
                'fax' => '02-23829019',
				'contactAddress' => $addressList[0]->id,
			],
			[
				'tel' => '03-9382001',
                'fax' => '03-9334569',
				'contactAddress' => $addressList[1]->id,
			],
			[
				'tel' => '04-6386691',
                'fax' => '04-6355779',
				'contactAddress' => $addressList[2]->id,
			],
			[
				'tel' => '04-2278909',
                'fax' => '04-2226789',
				'contactAddress' => $addressList[3]->id,
			],
			[
				'tel' => '06-8971334',
                'fax' => '06-8897657',
				'contactAddress' => $addressList[4]->id,
			],
			[
				'tel' => '03-8679080',
                'fax' => '03-8686556',
				'contactAddress' => $addressList[5]->id,
			],
		
		];

		foreach ($infoList as $key => $value) {
			ContactInfo::create($value);
		}
    }
}
