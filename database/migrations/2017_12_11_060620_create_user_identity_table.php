<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserIdentityTable extends Migration
{
    public function up()
    {
        Schema::create('user_identity', function(Blueprint $table)
        {
            $table->integer('user_id');
            $table->integer('identity_id');
            $table->primary(['user_id','identity_id']);

            $table->foreign('user_id')->references('id')
            ->on('users')->onDelete('cascade');

            $table->foreign('identity_id')->references('id')
            ->on('identities')->onDelete('cascade');

        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_identity');
    }
}
