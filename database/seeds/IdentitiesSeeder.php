
<?php

use App\Identity;
use Illuminate\Database\Seeder;

class IdentitiesSeeder extends Seeder 
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
				'name' => '舊生',
			],
			[
				'name' => '各級學校在校生',
			],
			[
				'name' => '原住民',
			],
            [
				'name' => '65歲以上銀髮族',
			],
            [
				'name' => '身心障礙朋友',
			],
            [
				'name' => '低收入戶',
			],
			[
				'name' => '宗教師',
			],
			
			[
				'name' => '慈濟志業體同仁',
                'member' => true
			],
            [
				'name' => '慈誠委員',
                'member' => true
			],
            [
				'name' => '榮譽董事',
                'member' => true
			],

		];
  
		foreach ($items as $key => $value) {
			Identity::create($value);
		}
	}
}
	
