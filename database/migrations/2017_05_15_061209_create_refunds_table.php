<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->integer('signup_id')->unsigned();
			$table->primary('signup_id');
			$table->foreign('signup_id')->references('id')->on('signups')->onDelete('cascade');
            
            $table->date('date');
            $table->string('number')->nullable();
            $table->integer('courses_total')->unsigned();
            $table->integer('courses_done')->unsigned();
            $table->float('points',3,2)->unsigned()->nullable();
            $table->decimal('tuition', 8, 2);  
            $table->decimal('cost', 8, 2)->nullable();
            $table->decimal('charge', 8, 2)->nullable();
            $table->integer('pay_by')->unsigned();
            $table->string('bank_branch')->nullable();
            $table->string('account_owner')->nullable();
            $table->string('account_number')->nullable();
            $table->integer('status')->default(0);
            $table->integer('updated_by')->unsigned()->nullable();
            $table->boolean('removed')->default(false);
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
        Schema::dropIfExists('refunds');
    }
}
