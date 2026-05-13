<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('clt_layups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('total_thickness', 8, 4)->nullable();
            $table->decimal('total_width', 8, 4)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['supplier_id', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('clt_layups');
    }
};
