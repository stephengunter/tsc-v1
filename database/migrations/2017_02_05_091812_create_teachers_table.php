<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('teachers', function (Blueprint $table) {
			$table->integer('user_id')->unsigned();
			$table->primary('user_id');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			
			
			$table->text('experiences')->nullable();
			$table->string('education')->nullable();
			$table->string('certificate')->nullable();;
			$table->string('specialty')->nullable();
			$table->string('job')->nullable();
            $table->string('jobtitle')->nullable();
			$table->text('description')->nullable();
			$table->boolean('active')->default(false);
			$table->boolean('reviewed')->default(false);

			$table->boolean('group')->default(false);
			$table->text('teacher_ids')->nullable();

			$table->boolean('removed')->default(false);
			$table->date('join_date')->nullable();

			$table->integer('updated_by')->unsigned()->nullable();
			$table->integer('reviewed_by')->unsigned()->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('teachers');
	}
}
