
<?php

use App\User;
use App\Profile;
use App\Center;
use App\Admin;

use Faker\Factory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder 
{
	
	public function run()
	{

        $faker = Factory::create();
		
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
            
		}  //end for	
		


				
	}

	

}
	
