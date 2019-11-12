<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('business_id', 100)->unique();
            $table->string('image', 255);
            $table->string('title', 1000);
            $table->text('body');
            $table->string('full_url', 1000);
            $table->json('tags')->nullable();
            $table->dateTime('published_at');
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
