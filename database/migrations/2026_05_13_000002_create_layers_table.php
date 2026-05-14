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
        Schema::create('layers', function (Blueprint $table) {
            $table->id();
            $table->string('layup_id', 20);
            $table->integer('layer_order');
            $table->decimal('thickness', 8, 4);
            $table->decimal('width', 8, 4);
            $table->decimal('angle', 6, 2);
            $table->timestamps();

            $table->foreign('layup_id')->references('layup_id')->on('layups')->onDelete('cascade');
            $table->unique(['layup_id', 'layer_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layers');
    }
};
