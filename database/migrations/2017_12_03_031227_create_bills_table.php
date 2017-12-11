<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->nullable(); 

            $table->decimal('amount', 8, 2)->nullable(); 
            $table->integer('discount_id')->unsigned()->nullable();
            $table->integer('points')->unsigned()->nullable();
            $table->string('discount')->nullable();  
            $table->string('identity_id')->nullable();  

            $table->string('signup_ids')->nullable(); 

            $table->integer('pay_way')->nullable();          
            $table->integer('status')->default(0);

            $table->boolean('removed')->default(false);
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
        Schema::dropIfExists('bills');
    }
}
