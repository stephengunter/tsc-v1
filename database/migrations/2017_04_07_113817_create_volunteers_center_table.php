<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteersCenterTable extends Migration
{
    public function up()
    {
      Schema::create('center_volunteer', function(Blueprint $table)
        {   
            $table->integer('center_id');
            $table->integer('user_id');
            
            $table->primary(['center_id','user_id']);

            

            $table->foreign('center_id')->references('id')
                                        ->on('centers')->onDelete('cascade');

            $table->foreign('user_id')->references('user_id')
                                      ->on('volunteers')->onDelete('cascade');

                                       

        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('center_volunteer');
    }
}
