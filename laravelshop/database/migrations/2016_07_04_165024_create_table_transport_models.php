<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransportModels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_models', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->integer('manufacturer')->nullable();
            $table->text('description')->nullable();
            $table->float('price')->nullable();
            $table->integer('count')->default('0');
            $table->integer('image')->nullable()->unsigned();
            $table->integer('country')->nullable();

            $table->integer('category')->nullable();
            $table->integer('scale')->nullable();
            $table->integer('material')->nullable();
            $table->integer('envelop')->nullable();
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
        Schema::drop('transport_models');
    }
}
