<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactInfoesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('contactinfoes', function (Blueprint $table) {
			$table->increments('id');
			$table->string('tel')->nullable();
			$table->string('fax')->nullable();

			$table->integer('residenceAddress')->unsigned()->nullable();
			$table->integer('contactAddress')->unsigned()->nullable();

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
		Schema::dropIfExists('contactinfoes');
	}
}
