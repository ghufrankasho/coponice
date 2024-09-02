<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Sliders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sliders', function (Blueprint $table) {
            
            $table->id();
            $table->enum('type',[1,2,3,4,5])->nullable();
            $table->string('link')->nullable();
            $table->string('alt')->nullable();
            $table->string('image')->default(null)->nullable();
            
            // new columns update
            $table->boolean('visible')->default(true);
            $table->date('expire_date')->nullable();
            $table->integer('sorting')->nullable();//default value in store function from me :)
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
        Schema::dropIfExists('sliders');
    }
}