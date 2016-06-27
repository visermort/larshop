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
            $table->increments('id');
            $table->string('table_name',32);
            $table->string('field_name',32);
            $table->string('value');
            $table->timestamps();
            $table->unique(['id','table_name','field_name']);
            $table->unique(['table_name','field_name','value']);
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
