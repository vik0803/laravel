<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->nullableTimestamps();
            $table->integer('parent')->unsigned()->nullable();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('slug');
            $table->string('description')->nullable();
            $table->string('content')->nullable();
            $table->integer('order')->unsigned()->default(0);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_category')->default(false);
            $table->boolean('is_dropdown')->default(false);

            $table->foreign('parent')->references('id')->on('pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pages');
    }
}
