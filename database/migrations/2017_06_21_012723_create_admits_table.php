<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
			$table->foreign('course_id')->references('course_id')->on('admissions')->onDelete('cascade');

            $table->integer('signup_id')->unsigned();

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
        Schema::dropIfExists('admits');
    }
}
