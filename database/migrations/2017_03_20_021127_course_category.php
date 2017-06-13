<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CourseCategory extends Migration
{
    public function up()
    {
       Schema::create('course_category', function(Blueprint $table)
        {
             $table->integer('course_id');
             $table->integer('category_id');
             $table->primary(['course_id','category_id']);

              $table->foreign('course_id')->references('id')
                    ->on('courses')->onDelete('cascade');

                $table->foreign('category_id')->references('id')
                ->on('categories')->onDelete('cascade');

        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_category');
    }
}
