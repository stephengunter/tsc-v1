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
				'name' => 'Owner',
				'display_name' => '老闆',
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
				'name' => 'Student',
				'display_name' => '學生',
				'can_add' => 0,
				'importance' => 66,
				'style'=>'default'
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
