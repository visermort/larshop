<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableHeadclothes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('headclothes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('manufacturer')->nullable();
            $table->text('description')->nullable();
            $table->float('price')->nullable();
            $table->integer('count')->default('0');
            $table->integer('image')->nullable()->unsigned();
            $table->integer('country')->nullable();

            $table->integer('category')->nullable();
            $table->integer('style')->nullable();
            $table->integer('sex')->nullable();
            $table->integer('color')->nullable();
            $table->integer('fabric')->nullable();
            $table->integer('size')->nullable();

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
        Schema::drop('headclothes');
    }
}
