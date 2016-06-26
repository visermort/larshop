<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDict extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dict', function (Blueprint $table) {
            $table->string('table',20);
            $table->string('field',20);
            $table->integer('id')->unsigned();
            $table->string('value');
            $table->timestamps();
            $table->primary('table','field','id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dict');
    }
}
