<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseNoticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
    {
       Schema::create('course_notice', function(Blueprint $table)
        {
             $table->integer('course_id');
             $table->integer('notice_id');
             $table->primary(['course_id','notice_id']);

              $table->foreign('course_id')->references('id')
                    ->on('courses');

                $table->foreign('notice_id')->references('id')
                ->on('notices');

        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_notice');
    }
}
