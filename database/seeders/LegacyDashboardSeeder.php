<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyDashboardSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->truncate();
        DB::table('patient_data')->truncate();
        DB::table('protocolo_data')->truncate();

        $now = Carbon::now();

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Coordinación Quirúrgica',
                'username' => 'coordinacion',
                'email' => 'coordinacion@example.test',
                'password' => bcrypt(Str::random(12)),
                'created_at' => $now,
                'updated_at' => $now,
                'especialidad' => 'Oftalmología',
                'subespecialidad' => 'Retina',
                'is_approved' => true,
                'biografia' => 'Coordina los turnos y plantillas quirúrgicas.',
                'firma' => null,
            ],
            [
                'id' => 2,
                'name' => 'Dra. Ana Pérez',
                'username' => 'aperez',
                'email' => 'ana@example.test',
                'password' => bcrypt(Str::random(12)),
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now,
                'especialidad' => 'Oftalmología',
                'subespecialidad' => 'Catarata',
                'is_approved' => false,
                'biografia' => 'Subespecialista en catarata infantil.',
                'firma' => '/storage/firmas/ana.png',
            ],
        ]);

        DB::table('patient_data')->insert([
            [
                'hc_number' => 'HC-001',
                'fname' => 'Luis',
                'lname' => 'Mora',
                'lname2' => 'Pérez',
                'ciudad' => 'Quito',
                'afiliacion' => 'IESS',
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(10),
            ],
            [
                'hc_number' => 'HC-002',
                'fname' => 'Andrea',
                'lname' => 'Salazar',
                'lname2' => 'Cruz',
                'ciudad' => 'Cuenca',
                'afiliacion' => 'Particular',
                'created_at' => $now->copy()->subDays(9),
                'updated_at' => $now->copy()->subDays(9),
            ],
        ]);

        $procedures = [
            ['hc' => 'HC-001', 'membrete' => 'FACOEMULSIFICACIÓN', 'fecha' => Carbon::parse('2024-07-01 08:00:00')],
            ['hc' => 'HC-002', 'membrete' => 'FACOEMULSIFICACIÓN', 'fecha' => Carbon::parse('2024-07-02 10:30:00')],
            ['hc' => 'HC-001', 'membrete' => 'VITRECTOMÍA', 'fecha' => Carbon::parse('2024-07-03 09:15:00')],
            ['hc' => 'HC-002', 'membrete' => 'FACOEMULSIFICACIÓN', 'fecha' => Carbon::parse('2024-07-03 11:45:00')],
            ['hc' => 'HC-001', 'membrete' => 'VITRECTOMÍA', 'fecha' => Carbon::parse('2024-07-05 13:00:00')],
        ];

        foreach ($procedures as $index => $procedure) {
            DB::table('protocolo_data')->insert([
                'hc_number' => $procedure['hc'],
                'membrete' => $procedure['membrete'],
                'procedimiento_id' => Str::slug($procedure['membrete']),
                'fecha_inicio' => $procedure['fecha'],
                'form_id' => 'FORM-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'created_at' => $procedure['fecha'],
                'updated_at' => $procedure['fecha'],
            ]);
        }
    }
}
