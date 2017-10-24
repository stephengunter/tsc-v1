
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
		

		// $centers=Center::all();
		// foreach($centers as $center){
		// 	$roleName='Owner';
		// 	$name=$center->code . '_' . 'boss';
		// 	$email=$name . '@gmail.com';
		// 	$fullname= $center->name. '_' . '組長';
		// 	$dob='';
		// 	$gender=1;
		// 	$phone='';
		// 	$this->addAdmin($center, $name , $email, $phone ,$fullname, $dob, $gender, $roleName);
		// }


		$this->addDev();

				
	}

	private function addDev(){
		$user=User::create([
			'name' => 'stephen',
			'email' =>'traders.com.tw@gmail.com',
			'phone' => '0936060049',
			'password' => 'secret',
			'email_confirmed' => true,
			'remember_token' => str_random(10),
		]);
		$profile=new Profile([
			'user_id' => $user->id,					
			'fullname'=> '何金水',
			'dob' =>'1979-3-12',
			'gender' => 1,
		]);
		$user->profile()->save($profile);

		$roleName='Dev';
		$user->addRole($roleName);
		
	}
	private function addAdmin($center, $name , $email, $phone ,$fullname, $dob, $gender, $roleName)
	{
		$user=User::create([
			'name' => $name,
			'email' => $email,
			'phone' => $phone,
			'password' => '000000',
			'email_confirmed' => true,
			'remember_token' => str_random(10),
		]);
		$profile=new Profile([
			'fullname'=> $fullname,
			'dob' => $dob,
			'gender' => $gender,
		]);
		$user->profile()->save($profile);

		if($roleName){
			$user->addRole($roleName);
			$adminValues = [
				'role' => $roleName,	
				'user_id' => $user->id,
			];
			Admin::create($adminValues);
		}
		
	}

}
	
