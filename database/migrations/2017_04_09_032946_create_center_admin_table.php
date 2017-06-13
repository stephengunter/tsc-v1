<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCenterAdminTable extends Migration
{
    public function up()
    {
        Schema::create('center_admin', function(Blueprint $table)
        {   
            $table->integer('center_id');
            $table->integer('admin_id');
            $table->primary(['center_id','admin_id']);

            $table->foreign('center_id')->references('id')
            ->on('centers')->onDelete('cascade');

            $table->foreign('admin_id')->references('user_id')
            ->on('admins')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('center_admin');
    }
}
