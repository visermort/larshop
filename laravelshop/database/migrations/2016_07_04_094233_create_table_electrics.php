<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableElectrics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('electrics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('manufacturer')->nullable();
            $table->text('description')->nullable();
            $table->float('price')->nullable();
            $table->integer('count')->default('0');
            $table->integer('image')->nullable()->unsigned();
            $table->integer('country')->nullable();

            $table->integer('category')->nullable();
            $table->integer('power')->nullable();
            $table->integer('voltage')->nullable();
            $table->float('massa')->nullable();
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->float('depth')->nullable();
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
        Schema::drop('electrics');
    }
}
