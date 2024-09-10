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
        Schema::table('files', function (Blueprint $table) {
            $table->unsignedBigInteger('author_id');

            $table->foreign('author_id', 'foreign_author_id')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropForeign('foreign_author_id');
            $table->dropIndex('foreign_author_id');
            
            $table->dropColumn('author_id');
        });
    }
};
