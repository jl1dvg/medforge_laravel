<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('patient_data')) {
            Schema::create('patient_data', function (Blueprint $table) {
                $table->id();
                $table->string('hc_number')->unique();
                $table->string('fname')->nullable();
                $table->string('lname')->nullable();
                $table->string('lname2')->nullable();
                $table->date('fecha_nacimiento')->nullable();
                $table->string('ciudad')->nullable();
                $table->string('afiliacion')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('protocolo_data')) {
            Schema::create('protocolo_data', function (Blueprint $table) {
                $table->id();
                $table->string('hc_number');
                $table->string('membrete')->nullable();
                $table->string('procedimiento_id')->nullable();
                $table->timestamp('fecha_inicio')->nullable();
                $table->string('form_id')->nullable();
                $table->timestamps();

                $table->index('hc_number');
                $table->index('fecha_inicio');
            });
        }
    }

    public function down(): void
    {
        // No eliminamos tablas legacy para preservar datos.
    }
};
