<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCloses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('closes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('manufacturer')->nullable();
            $table->text('description')->nullable();
            $table->float('price')->nullable();
            $table->integer('count')->default('0');
            $table->integer('image')->nullable()->unsigned();
            $table->integer('size')->nullable();
            $table->integer('season')->nullable();
            $table->integer('sex')->nullable();
            $table->integer('category')->nullable();
            $table->integer('country')->nullable();
            $table->foreign('image')->references('id')
                ->on('images')
                ->onDelete('cascade');

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
        Schema::drop('closes');
    }
}
