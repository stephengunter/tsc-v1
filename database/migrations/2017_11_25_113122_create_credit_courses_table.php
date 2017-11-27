<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditCoursesTable extends Migration
{
    
    public function up()
    {
        return false;
        Schema::create('credit_courses', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('center_id')->unsigned();

            $table->string('type');
            $table->string('name');
            $table->string('number')->nullable();
            $table->string('time')->nullable();
            $table->string('location')->nullable();
            $table->string('tel')->nullable();

            $table->integer('parent')->unsigned()->default(0);

            $table->integer('credit_count')->default(0);   //學分數
            $table->decimal('credit_price', 8, 2)->nullable();   //學分單價
            $table->boolean('must')->default(false);  //必修

            
            $table->boolean('net_signup')->default(true);

		
			$table->date('begin_date')->nullable();
            $table->date('end_date')->nullable();
            
            	
            $table->decimal('signup_charge', 8, 2)->nullable();   //報名費
            $table->decimal('tuition', 8, 2)->nullable();   //學費
            $table->decimal('cost', 8, 2)->nullable();  //材料費
            $table->text('materials')->nullable();   //材料    槌子,榔頭,電鑽
            $table->text('description')->nullable();
            $table->text('target')->nullable();  //招生對象/資格

            $table->text('caution')->nullable();  // 注意事項

            $table->date('open_date')->nullable();    //開始報名
            $table->date('close_date')->nullable();   //截止報名
            $table->date('decision_date')->nullable();   //放榜日期
            $table->integer('limit')->nullable();    //人數上限 
            $table->integer('min')->default(0);    //人數下限

            

            
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
        Schema::dropIfExists('credit_courses');
    }
}
