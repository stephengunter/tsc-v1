<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Signup;
use App\Course;
use App\Discount;

class SignupSeeder extends Seeder
{
    public function run()
    { 
        $dates=[
            '2017-5-12','2017-5-14','2017-5-11',
            '2017-5-12','2017-5-19',
        ];
      for($i=0; $i<5; $i++){
        $course=Course::inRandomOrder()->first();
        $user=User::inRandomOrder()->first();
        $discount=Discount::inRandomOrder()->first();

        $tuition=$course->tuition;
        $points=$discount->points;
        $tuition=$tuition*$points/100;
        $signup=Signup::create([
            'course_id' => $course->id,
                'user_id' => $user->id,
                'date' => $dates[$i],
                'tuition' => $tuition,
                'cost' => $course->cost,
                'points' => $points,
                'discount' => $discount->name,
                'identity' => $discount->identityName(),
                'updated_by' => $user->id,
            ]);
      }  

    }
}
