<?php

use App\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$role = [
			[
				'name' => 'Dev',
				'hide' => 1,
				'display_name' => '開發者',
				'importance' => 106,
				'style'=>'info'

			],
			[
				'name' => 'Owner',
				'display_name' => '組長',
				'importance' => 99,
				'style'=>'danger'

			],
			[
				'name' => 'Admin',
				'display_name' => '管理員',
				'importance' => 88,
				'style'=>'warning'
			],
			[
				'name' => 'Teacher',
				'display_name' => '教師',
				'importance' => 77,
				'style'=>'success'
			],
			
			[
				'name' => 'Volunteer',
				'display_name' => '志工',
				'importance' => 55,
				'style'=>'info'
			],

		];

		foreach ($role as $key => $value) {
			Role::create($value);
		}
	}
}
