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
        Schema::create('marcacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('users');
            $table->enum('tipo_marcacion', [
                'ingreso', 
                'salida', 
                'almuerzo_inicio', 
                'almuerzo_fin'
            ]);
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marcacions');
    }
};
