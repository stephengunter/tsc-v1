<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('profiles', function (Blueprint $table) {
			$table->integer('user_id')->unsigned();
			$table->primary('user_id');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

			$table->string('fullname')->nullable();;
			$table->string('SID')->nullable();
			$table->boolean('gender')->default(true);
			$table->date('dob')->nullable();
			$table->integer('photo_id')->unsigned()->nullable(); 
			$table->integer('title_id')->unsigned()->nullable(); 

			$table->integer('updated_by')->unsigned()->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('profiles');
	}
}
