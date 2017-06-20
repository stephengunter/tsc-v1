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
            $table->integer('term_id')->unsigned();

            $table->integer('center_id')->unsigned();

            $table->string('name');
            $table->string('number');

            $table->integer('credit_count')->default(0);   //學分數
            $table->boolean('net_signup')->default(true);

		
			$table->date('begin_date');
            $table->date('end_date');
            $table->integer('weeks');
            $table->integer('hours');
            	

            $table->decimal('tuition', 8, 2)->nullable();   //學費
            $table->decimal('cost', 8, 2)->nullable();  //材料費
            $table->string('materials')->nullable();   //材料    槌子,榔頭,電鑽
            $table->text('description')->nullable();
            $table->text('target')->nullable();

            $table->date('open_date')->nullable();    //開始報名
            $table->date('close_date')->nullable();   //截止報名
            $table->integer('limit')->nullable();    //人數上限 
            $table->integer('min')->default(0);    //人數下限

            
            $table->integer('display_order')->default(0);
            $table->integer('photo_id')->unsigned()->nullable();

            $table->boolean('reviewed')->default(false);
            $table->boolean('active')->default(false);            
            $table->boolean('removed')->default(false);
            $table->integer('updated_by')->unsigned()->nullable();
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
