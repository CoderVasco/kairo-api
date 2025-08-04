<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqsTable extends Migration
{
    /**
     * Execute a migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id(); // id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('user_id'); // user_id BIGINT UNSIGNED
            $table->text('question'); // question TEXT NOT NULL
            $table->text('answer'); // answer TEXT NOT NULL
            $table->json('embedding')->nullable(); // embedding JSON NULLABLE
            $table->timestamps(); // created_at TIMESTAMP, updated_at TIMESTAMP

            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faqs');
    }
}