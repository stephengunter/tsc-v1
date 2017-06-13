<?php

use Illuminate\Database\Seeder;
use App\Center;
use App\Classroom;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         
        $rooms = [
			[
				'name' => '電腦教室A',
				'active' => 1,
                'ps'=>'',
			],
			[
				'name' => '大型教室A',
				'active' => 1,
                  'ps'=>'',
			],
			[
				'name' => '禪思教室',
				'active' => 1,
                  'ps'=>'',
			],
			[
				'name' => '語言教室A',
				'active' => 1,
                  'ps'=>'',
			],
            [
				'name' => '電腦教室B',
				'active' => 1,
                  'ps'=>'',
			],			
		];

        $centers=Center::all();     
        $max=count($rooms) -1 ;
         foreach ($centers as $center) {
             $index= mt_rand(0, $max);
             $classroom=new Classroom($rooms[$index]);
			 $center->classrooms()->save($classroom);

              $index= mt_rand(0, $max);
             $classroom=new Classroom($rooms[$index]);
              $center->classrooms()->save($classroom);
		}
		
    }
}
