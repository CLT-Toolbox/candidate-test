<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clt_layups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['supplier_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clt_layups');
    }
};
