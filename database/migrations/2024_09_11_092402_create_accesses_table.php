<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accesses', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->string('file_id', 10);


            $table->foreign('user_id', 'foreign_user_id')->on('users')->references('id');
            $table->foreign('file_id', 'foreign_file_id')->on('files')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accesses');
    }
};
