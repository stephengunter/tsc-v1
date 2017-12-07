<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCenterTable extends Migration
{
    public function up()
    {
        Schema::create('centers', function (Blueprint $table) {			
            $table->increments('id');
            $table->boolean('head')->default(false);
            $table->boolean('oversea')->default(false);
            $table->integer('area_id')->unsigned()->nullable();

            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('course_tel')->nullable();

            $table->text('rule')->nullable();

            $table->integer('contact_info')->unsigned()->nullable();
            $table->integer('photo_id')->unsigned()->nullable();
            $table->boolean('active')->default(true);
            $table->integer('display_order')->default(0);

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
        	Schema::dropIfExists('centers');
    }
}
