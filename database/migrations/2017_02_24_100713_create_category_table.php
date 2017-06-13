<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryTable extends Migration
{
   public function up()
    {
        Schema::create('categories', function (Blueprint $table) {			
            $table->increments('id');
            $table->integer('parent')->unsigned()->default(0);
            
			$table->string('name');			
            $table->integer('order')->default(0);
            $table->string('icon')->nullable(); 

            $table->boolean('public')->default(false);  

            $table->boolean('active')->default(true);

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
        	Schema::dropIfExists('categories');
    }
}
