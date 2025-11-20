<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('patient_data', function (Blueprint $table) {
            if (! Schema::hasColumn('patient_data', 'mname')) {
                $table->string('mname')->nullable()->after('fname');
            }

            if (! Schema::hasColumn('patient_data', 'celular')) {
                $table->string('celular')->nullable()->after('afiliacion');
            }
        });

        if (! Schema::hasTable('consulta_data')) {
            Schema::create('consulta_data', function (Blueprint $table) {
                $table->id();
                $table->string('hc_number');
                $table->string('form_id')->nullable();
                $table->dateTime('fecha')->nullable();
                $table->json('diagnosticos')->nullable();
                $table->text('examen_fisico')->nullable();

                $table->index(['form_id', 'hc_number']);
                $table->index(['hc_number', 'fecha']);
            });
        }

        if (! Schema::hasTable('patient_identity_certifications')) {
            Schema::create('patient_identity_certifications', function (Blueprint $table) {
                $table->id();
                $table->string('patient_id');
                $table->string('document_number')->nullable();
                $table->string('document_type')->default('cedula');
                $table->string('signature_path')->nullable();
                $table->json('signature_template')->nullable();
                $table->string('face_image_path')->nullable();
                $table->json('face_template')->nullable();
                $table->string('status')->default('verified');
                $table->dateTime('last_verification_at')->nullable();
                $table->string('last_verification_result')->nullable();
                $table->dateTime('expired_at')->nullable();
                $table->timestamps();

                $table->unique('patient_id');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::table('patient_data', function (Blueprint $table) {
            if (Schema::hasColumn('patient_data', 'mname')) {
                $table->dropColumn('mname');
            }

            if (Schema::hasColumn('patient_data', 'celular')) {
                $table->dropColumn('celular');
            }
        });

        Schema::dropIfExists('consulta_data');
        Schema::dropIfExists('patient_identity_certifications');
    }
};
