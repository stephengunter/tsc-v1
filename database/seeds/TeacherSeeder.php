<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;
use App\Teacher;
use App\ContactInfo;
use App\Center;


class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $teacherValues = [
			[
				'experiences' => '水電公會理事',
                'education' => '高職',
				'specialty' => '水電工程',
                'job' => '盛安消防',
                'jobtitle' => '技工',
                'description' =>'豪鉅工程有限公司事業已達二十餘年之久，向來堅持品質與服務為優先之經營理念，
不論在施工、材料、或品質控管都有一定之專業水準，未來公司在經營之方向
與理念，就是把專業、用心、品質、服務 帶給每位客戶。',
			],
			[
				'experiences' => '紐約愛樂交響樂團',
                'education' => '大學',
				'specialty' => '大提琴',
                'job' => '銀藝音樂教室',
                'jobtitle' => '顧問',
                'description' =>'在弦樂家族中，由於大提琴與一般人說話的音域、
                音色較為接近，因此有人認為大提琴的聲音最接近人聲。此外，比起小提琴高亢、細瘦的音色，
                大提琴的低沉、渾厚的聲音更受到一般人青睞，而在管弦樂團中，
                大提琴更是不可或缺的樂器',
			],
			[
				'experiences' => '加州大學交換生',
                'education' => '碩士',
				'specialty' => '西班牙語',
                'job' => '天下文化',
                'jobtitle' => '翻譯',
                'description' =>'西班牙語是國際溝通上第二常用的語言，包括聯合國、WTO等世界組織均將其作為正式官方語言',
			],
			[
				'experiences' => '農委會顧問',
                'education' => '碩士',
				'specialty' => '有機農業',
                'job' => '臺大農藝系',
                'jobtitle' => '教授',
                'description' =>'從罐裝咖啡開始著手研究咖啡已逾十年。更提出一套台灣「精品咖啡」的認證標準，「無毒、有料、健康」的標準為台灣培訓咖啡官能專業鑑定人員，帶動台灣咖啡飲料製作與官能鑑定國際水平。',
			],
			[
				'experiences' => '中華中醫藥生技國際發展協會顧問',
                'education' => '碩士',
				'specialty' => '中醫',
                'job' => '添盛中醫',
                'jobtitle' => '醫師',
                'description' =>'於民國51年創立深浦藥房，並於民國69年成立深浦中醫診所，以五十餘年臨床經驗，創造出深浦系列產品的口碑與見證奇蹟，歷經社會大眾的考驗，受到專業及大眾的肯定。',
			],
		
		
		];
        $count=5;
        $users=User::take($count)->get();
        $contacts=ContactInfo::take($count)->get();
        $names=['陳大慶','李明博','林美珠','蔡偉民','陳曉鈴'];
        $genders=[true, true, false, true, false];
        $dobs=['1975-8-17', '1960-4-9', '1966-7-22', '1958-3-6', '1970-1-15'];
        $sidList=['F125678901','A121672567','C225677890','K125615812','S219882451'];
        $phones=['0922567900','0918621980','0932255661','0925667890','0935190732'];

        $roleName='Teacher';
        $role=Role::where('name',$roleName)->firstOrFail();

        $centers=Center::all();
        

        for($i = 0; $i < $count; ++$i) {
            $user=$users[$i];
            $user->phone=$phones[$i];
            $user->contact_info=$contacts[$i]->id;

            $user->save();

            $user->profile->fullname=$names[$i];
            $user->profile->SID=$sidList[$i];
            $user->profile->gender=$genders[$i];
            $user->profile->dob=$dobs[$i];

            $user->profile->save();

            

            $teacher=new Teacher($teacherValues[$i]); 
          
            $teacher->active=true;
            $teacher->reviewed=true;
            $user->teacher()->save($teacher); 

            $user->attachRole($role);  

            $teacher=$user->teacher; 
            $center=$centers->random();
            $teacher->centers()->attach($center);

            


        }
    }
}
