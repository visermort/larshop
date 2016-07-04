<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableContactLenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_lenses', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->integer('manufacturer')->nullable();
            $table->text('description')->nullable();
            $table->float('price')->nullable();
            $table->integer('count')->default('0');
            $table->integer('image')->nullable()->unsigned();
            $table->integer('country')->nullable();

            $table->integer('type')->nullable();
            $table->integer('material')->nullable();
            $table->integer('diameter')->nullable();
            $table->integer('ufprotect')->nullable();
            $table->integer('dioptleft')->nullable();
            $table->integer('dioptright')->nullable();
            $table->integer('curve')->nullable();

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
        Schema::drop('contact_lenses');
    }
}
