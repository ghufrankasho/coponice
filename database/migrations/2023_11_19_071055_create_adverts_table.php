<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('link')->nullable();
            $table->string('image')->default(null)->nullable();
            $table->string('description')->nullable();
            $table->integer('counter')->default(0)->nullable(true);
            $table->integer('discount')->default(0)->nullable(true);
            $table->string('code')->nullable();
            $table->boolean('type')->nullable();//true :code, false:offer,null:spicial
            $table->boolean('main')->nullable();
            $table->foreignId('category_id')->constrained('categories','id')->default(null)->nullable();
            
            // new columns update
            $table->boolean('visible')->default(true);
            $table->date('expire_date')->nullable();
            $table->string('short_description')->nullable();
            $table->timestamp('timer_start')->nullable();
            $table->timestamp('timer_end')->nullable();
            // seo updated 
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->string('seo_image')->default(null)->nullable();
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
        Schema::dropIfExists('adverts');
    }
}