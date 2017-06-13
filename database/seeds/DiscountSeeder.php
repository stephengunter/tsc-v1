
<?php

use App\Identity;
use App\Discount;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder 
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		 $items = [];

         $identity=Identity::where('name','舊生')->first();
         $oldStudent=[
             'identity_id' => $identity->id,
             'key' => 'back',
             'name' => '舊生',
             'points' => 90,
             'active' => true
         ];
         array_push($items, $oldStudent);

         $identity=Identity::where('name','身心障礙朋友')->first();
         $handy=[
             'identity_id' => $identity->id,
             'key' => 'handy',
             'name' => '身心障礙朋友',
             'points' => 85,
             'active' => true
         ];
         array_push($items, $handy);

        
         $member=[
             'points' => 90,
             'key' => 'member',
             'name' => '慈濟會員',
             'active' => true
         ];
         array_push($items, $member);
         
		
  
		foreach ($items as $key => $value) {
			Discount::create($value);
		}
	}
}
	
