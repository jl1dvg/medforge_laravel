<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('visitas')) {
            Schema::create('visitas', function (Blueprint $table) {
                $table->id();
                $table->string('hc_number');
                $table->date('fecha_visita')->nullable();
                $table->time('hora_llegada')->nullable();
                $table->string('usuario_registro')->nullable();
                $table->timestamps();

                $table->index('hc_number');
                $table->index('fecha_visita');
            });
        }

        if (! Schema::hasTable('procedimiento_proyectado')) {
            Schema::create('procedimiento_proyectado', function (Blueprint $table) {
                $table->id();
                $table->string('hc_number');
                $table->unsignedBigInteger('visita_id')->nullable();
                $table->string('form_id')->unique();
                $table->string('procedimiento_proyectado')->nullable();
                $table->string('procedimiento_id')->nullable();
                $table->dateTime('fecha')->nullable();
                $table->string('hora')->nullable();
                $table->string('estado_agenda')->nullable();
                $table->string('doctor')->nullable();
                $table->string('id_sede')->nullable();
                $table->string('sede_departamento')->nullable();
                $table->timestamps();

                $table->index(['hc_number', 'fecha']);
                $table->index(['fecha', 'estado_agenda']);
            });
        }

        if (! Schema::hasTable('procedimiento_proyectado_estado')) {
            Schema::create('procedimiento_proyectado_estado', function (Blueprint $table) {
                $table->id();
                $table->string('form_id');
                $table->string('estado');
                $table->dateTime('fecha_hora_cambio')->nullable();
                $table->timestamps();

                $table->index(['form_id', 'estado']);
                $table->index('fecha_hora_cambio');
            });
        }

        if (! Schema::hasTable('solicitud_procedimiento')) {
            Schema::create('solicitud_procedimiento', function (Blueprint $table) {
                $table->id();
                $table->string('hc_number');
                $table->string('procedimiento')->nullable();
                $table->string('tipo')->nullable();
                $table->string('form_id')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('fecha')->nullable();
                $table->string('turno')->nullable();

                $table->index(['hc_number', 'created_at']);
                $table->index('form_id');
            });
        }

        if (! Schema::hasTable('prefactura_paciente')) {
            Schema::create('prefactura_paciente', function (Blueprint $table) {
                $table->id();
                $table->string('hc_number');
                $table->string('cod_derivacion')->nullable();
                $table->string('form_id')->nullable();
                $table->json('procedimientos')->nullable();
                $table->dateTime('fecha_creacion')->nullable();
                $table->dateTime('fecha_registro')->nullable();
                $table->date('fecha_vigencia')->nullable();

                $table->index('hc_number');
                $table->index('form_id');
                $table->index('fecha_creacion');
            });
        }
    }

    public function down(): void
    {
        // Legacy tables are intentionally preserved.
    }
};
