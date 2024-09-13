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
        Schema::table('accesses', function (Blueprint $table) {
            $table->dropForeign('foreign_file_id');
            $table->string('file_id', 10)->collation('utf8mb4_bin')->change();
        });
        Schema::table('files', function (Blueprint $table) {
            $table->string('id', 10)->collation('utf8mb4_bin')->change();
        });

        Schema::table('accesses', function (Blueprint $table) {
            $table->foreign('file_id', 'foreign_file_id')->on('files')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accesses', function (Blueprint $table) {
            $table->dropForeign('foreign_file_id');
            $table->string('file_id', 10)->change();
        });
        Schema::table('files', function (Blueprint $table) {
            $table->string('id', 10)->change();
        });

        Schema::table('accesses', function (Blueprint $table) {
            $table->foreign('file_id', 'foreign_file_id')->on('files')->references('id');
        });
    }
};
