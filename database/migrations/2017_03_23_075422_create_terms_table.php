<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('year');
            $table->integer('order');
            $table->string('name');
            $table->integer('number');

            $table->date('open_date')->nullable(); 
            $table->date('bird_date')->nullable();  
            $table->date('close_date')->nullable();  
            
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
        Schema::dropIfExists('terms');
    }
}
