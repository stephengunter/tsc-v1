<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('name')->nullable();

            $table->integer('center_id')->unsigned()->nullable();

            $table->integer('identity_id')->unsigned()->nullable();

            $table->integer('course_count')->default(1);
            $table->integer('points_one')->unsigned()->nullable();
            $table->integer('points_two')->unsigned()->nullable();
            
            $table->boolean('need_prove')->default(false);
            $table->boolean('all_courses')->default(false);

            $table->string('ps')->nullable();
            

            $table->integer('order')->default(0);
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
        Schema::dropIfExists('discounts');
    }
}
