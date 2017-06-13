
<?php

use App\User;
use App\Profile;
use Faker\Factory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder 
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{

         $faker = Factory::create();
		//  User::truncate();
        //  Profile::truncate();
		 foreach(range(1, 50) as $i) {
        
            $user = User::create([
                'name' => $faker->name,
			
                'email' => $faker->unique()->safeEmail,
			
				'password' => 'secret',
				'remember_token' => str_random(10),
            ]);

			 Profile::create([
                    'user_id' => $user->id,					
					'fullname'=> $faker->name,
                    'dob' => mt_rand(1950, 1995) . '-' .mt_rand(1, 12).'-'.mt_rand(1, 28),
                    'gender' => ( $i %2 == 0 ),
                ]);
            
        	}	

			$user = User::create([
					'name' => 'stephen',
					'email' =>'traders.com.tw@gmail.com',
					'phone' => '0936060049',
					'password' => 'secret',
					'email_confirmed' => true,
					'remember_token' => str_random(10),
                 ]);
			    Profile::create([
                    'user_id' => $user->id,					
					'fullname'=> '何金水',
                    'dob' =>'1979-3-12',
                    'gender' => 1,
                ]);

				
	}
}
	
