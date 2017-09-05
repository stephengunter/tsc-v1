<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('parent')->unsigned()->default(0);
            
            $table->date('date');
            
            $table->decimal('tuition', 8, 2);  
            $table->decimal('cost', 8, 2)->nullable();

            $table->integer('discount_id')->unsigned()->nullable();
            $table->integer('points')->unsigned()->nullable();
            $table->string('discount')->nullable();  
            $table->string('identity')->nullable();  

            $table->boolean('net_signup')->default(false);           
            $table->integer('status')->default(0);

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
        Schema::dropIfExists('signups');
    }
}
