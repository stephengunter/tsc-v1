
<?php

use App\Center;
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
        Discount::create([
            'name' => '新生',
            'points_one' => 90,
            'need_prove' => 0,
            'active' => 1,
        ]);
        Discount::create([
            'name' => '同時報名兩科以上',
            'course_count' => 2,
            'points_one' => 85,
            'need_prove' => 0,
            'active' => 1,
        ]);

        $center=Center::where('code','A')->first();

        
        Discount::create([
            'name' => '舊生、各級學校在校生、慈濟志業體同仁(含慈誠、委員、榮董)',
            'center_id' => $center->id,
            'points_one' => 85,
            'need_prove' => 1,
            'active' => 1,
        ]);
        Discount::create([
            'name' => '持中國信託蓮花卡刷卡繳費',
            'ps'=> '【本人】',
            'center_id' => $center->id,
            'points_one' => 90,
            'points_two' => 90,
            'need_prove' => 1,
            'active' => 1,
        ]);
        Discount::create([
            'name' => '銀髮族65歲以上、身心障礙朋友',
            'ps'=> '【繳費時請出示證件】',
            'center_id' => $center->id,
            'points_one' => 80,
            'points_two' => 80,
            'need_prove' => 1,
            'active' => 1,
        ]);
        Discount::create([
            'name' => '低收入戶',
            'ps'=> '【繳費時請提供政府開立相關證明】',
            'center_id' => $center->id,
            'points_one' => 50,
            'points_two' => 50,
            'need_prove' => 1,
            'active' => 1,
        ]);
        Discount::create([
            'name' => '宗教師',
            'ps'=> '【繳費時請提供相關證明】【不限課程】',
            'center_id' => $center->id,
            'points_one' => 50,
            'points_two' => 50,
            'need_prove' => 1,
            'all_courses' => 1,
            'active' => 1,
        ]);


       
	}
}
	
