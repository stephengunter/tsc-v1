<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('public')->default(false);
            $table->integer('user_id')->unsigned()->nullable();

            $table->string('owner')->nullable();
            $table->string('bank')->nullable();
            $table->string('branch')->nullable();
            $table->string('number');

            $table->boolean('removed')->default(false);
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('accounts');
    }
}
