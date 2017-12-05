<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTuitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tuitions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bill_id')->unsigned()->nullable();
            $table->integer('refund_id')->unsigned()->nullable();
           
            $table->boolean('confirmed')->default(false);
            
            $table->date('date');
            $table->integer('pay_by')->unsigned();
            $table->string('bank_branch')->nullable();
            $table->string('account_owner')->nullable();
            $table->string('account_number')->nullable();
            $table->decimal('amount', 8, 2);
            $table->string('ps')->nullable();
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
        Schema::dropIfExists('tuitions');
    }
}
