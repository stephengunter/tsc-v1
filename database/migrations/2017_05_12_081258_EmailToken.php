<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmailToken extends Migration
{
    
    public function up()
    {
        Schema::create('email_tokens', function (Blueprint $table) {
			$table->integer('user_id')->unsigned();
			$table->primary('user_id');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

			$table->string('token');
            $table->dateTime('expire_date');  
			
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
        Schema::dropIfExists('email_tokens');
    }
}
