
<?php

use App\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder 
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		 $accounts = [
			[
				'public' => 1,
				'owner'=> '慈大社推',
				'bank' => '第一銀行',
				'branch' => '花蓮分行',
                'number' => '221889099887',
			],
           

		];
  
		foreach ($accounts as $key => $value) {
			Account::create($value);
		}
	}
}
	
