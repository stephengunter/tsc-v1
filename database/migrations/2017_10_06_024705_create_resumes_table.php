<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResumesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resumes', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
			$table->primary('user_id');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->text('type')->nullable();

            $table->text('experiences')->nullable();
			$table->string('education')->nullable();
			$table->string('certificate')->nullable();;
			$table->string('specialty')->nullable();
			$table->string('job')->nullable();
            $table->string('jobtitle')->nullable();
			$table->text('description')->nullable();
			$table->boolean('active')->default(false);
			$table->boolean('reviewed')->default(false);
            
            $table->boolean('removed')->default(false);
            
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
    public function down()
    {
        Schema::dropIfExists('resumes');
    }
}
