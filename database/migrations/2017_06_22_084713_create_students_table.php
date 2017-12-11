<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();

            $table->integer('user_id')->unsigned();

            $table->integer('identity_id')->nullable(); 
            $table->boolean('confirmed')->default(false);

            $table->string('number')->nullable();
            $table->date('join_date');
            $table->date('out_date')->nullable();
            $table->boolean('active')->default(true);
            $table->text('ps')->nullable();

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
        Schema::dropIfExists('students');
    }
}
