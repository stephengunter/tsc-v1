<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');

            $table->integer('classroom_id')->unsigned()->nullable();

            $table->integer('order')->unsigned()->nullable();
            $table->integer('status')->default(0);    
            $table->date('date');
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->text('materials')->nullable();
            $table->integer('on')->nullable();
            $table->integer('off')->nullable();
            $table->string('ps')->nullable();

            
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
        Schema::dropIfExists('lessons');
    }
}
