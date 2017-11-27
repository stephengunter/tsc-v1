<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {			
            $table->increments('id');

            $table->integer('term_id')->unsigned()->nullable(); 
            $table->integer('center_id')->unsigned()->nullable(); 

            $table->string('name');
            $table->string('level')->nullable(); 
            $table->string('number')->nullable(); 

            $table->boolean('group')->default(false); 

            //學分班欄位
            $table->boolean('credit')->default(false); 
            $table->integer('parent')->unsigned()->default(0);
            $table->integer('type_id')->unsigned()->nullable(); 
            $table->integer('college_id')->unsigned()->nullable(); 

            $table->string('time')->nullable();
            $table->string('location')->nullable();
            $table->string('tel')->nullable();
            $table->boolean('must')->default(false);
            $table->integer('credit_count')->default(0);   //學分數
            $table->decimal('credit_price', 8, 2)->nullable();   //學分單價            
            $table->decimal('signup_charge', 8, 2)->nullable();   //報名費
            
            $table->date('decision_date')->nullable();   //放榜日期
            //End 學分班欄位


            

            

            
            $table->boolean('net_signup')->default(true);

		
			$table->date('begin_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('weeks')->unsigned()->nullable();
            $table->integer('hours')->unsigned()->nullable();
            	
            
            $table->decimal('tuition', 8, 2)->nullable();   //學費
            $table->decimal('cost', 8, 2)->nullable();  //材料費
            $table->text('materials')->nullable();   //材料    槌子,榔頭,電鑽
            $table->text('description')->nullable();
            $table->text('target')->nullable();  //招生對象

            $table->text('caution')->nullable();  // 注意事項

            $table->date('open_date')->nullable();    //開始報名
            $table->date('close_date')->nullable();   //截止報名
            $table->integer('limit')->nullable();    //人數上限 
            $table->integer('min')->default(0);    //人數下限

            $table->boolean('discount')->default(true);

            $table->integer('display_order')->default(0);
            $table->integer('photo_id')->unsigned()->nullable();

            $table->boolean('reviewed')->default(false);
            $table->boolean('active')->default(true);            
            $table->boolean('removed')->default(false);
            
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('reviewed_by')->unsigned()->nullable();
			$table->timestamps();
		});
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        	Schema::dropIfExists('courses');
    }
}
