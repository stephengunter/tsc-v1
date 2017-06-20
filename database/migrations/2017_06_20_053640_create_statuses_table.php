<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->integer('course_id')->unsigned();
			$table->primary('course_id');
			$table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');

			$table->integer('signup')->default(-1);
            $table->boolean('register')->default(false);
            $table->integer('class')->default(0);

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
        Schema::dropIfExists('statuses');
    }
}
