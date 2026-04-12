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
        Schema::table('clt_layups', function (Blueprint $table) {
            $table->string('revision')->nullable();
            $table->enum('status', ['active', 'draft', 'archived'])->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clt_layups', function (Blueprint $table) {
            $table->dropColumn(['revision', 'status']);
        });
    }
};
