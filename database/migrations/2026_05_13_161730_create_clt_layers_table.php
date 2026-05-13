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
        Schema::create('clt_layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clt_layup_id')->constrained()->onDelete('cascade');
            $table->integer('layer_order');
            $table->decimal('thickness', 8, 4);
            $table->decimal('width', 8, 4);
            $table->decimal('angle', 8, 2);
            $table->string('material')->nullable();
            $table->timestamps();

            $table->unique(['clt_layup_id', 'layer_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('clt_layers');
    }
};
