<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('name');
            }

            if (! Schema::hasColumn('users', 'nombre')) {
                $table->string('nombre')->nullable()->after('username');
            }

            if (! Schema::hasColumn('users', 'especialidad')) {
                $table->string('especialidad')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'subespecialidad')) {
                $table->string('subespecialidad')->nullable()->after('especialidad');
            }

            if (! Schema::hasColumn('users', 'is_approved')) {
                $table->boolean('is_approved')->default(false)->after('password');
            }

            if (! Schema::hasColumn('users', 'biografia')) {
                $table->text('biografia')->nullable()->after('remember_token');
            }

            if (! Schema::hasColumn('users', 'firma')) {
                $table->string('firma')->nullable()->after('biografia');
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable()->after('firma');
            }
        });
    }

    public function down(): void
    {
        // Se conservan las columnas para evitar pérdida de datos legacy.
    }
};
