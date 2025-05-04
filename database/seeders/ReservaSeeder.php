<?php

namespace Database\Seeders;

use App\Models\Equipo;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReservaSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();
        $equipoIds = Equipo::pluck('id')->toArray();
        $recursoIds = Recurso::pluck('id')->toArray();

        if (empty($userIds) || (empty($equipoIds) && empty($recursoIds))) {
            $this->command->warn('No hay suficientes datos en las tablas para generar reservas.');
            return;
        }

        $reservations = [];

        for ($i = 0; $i < 20; $i++) {
            $maxAttempts = 10;
            $attempts = 0;
            do {
                $startHour = rand(8, 16);
                $startMinute = rand(0, 1) * 45;
                $startDate = Carbon::today()->setTime($startHour, $startMinute, 0);
                $endDate = (clone $startDate)->addMinutes(45);

                $overlap = false;
                foreach ($reservations as $reservation) {
                    if ($startDate->between($reservation['hora_inicio'], $reservation['hora_fin']) ||
                        $endDate->between($reservation['hora_inicio'], $reservation['hora_fin'])) {
                        $overlap = true;
                        break;
                    }
                }

                $attempts++;
                if ($attempts >= $maxAttempts) {
                    $this->command->warn('Se alcanzó el límite de intentos para evitar solapamiento.');
                    break;
                }
            } while ($overlap);

            $equipoId = !empty($equipoIds) && rand(0, 1) ? $equipoIds[array_rand($equipoIds)] : null;
            $recursoId = (!empty($recursoIds) && is_null($equipoId)) ? $recursoIds[array_rand($recursoIds)] : null;

            $reservation = [
                'hora_inicio' => $startDate,
                'hora_fin' => $endDate,
                'estado' => ['Por aprobar', 'Aprobada', 'No aprobada', 'Cancelada'][rand(0, 3)],
                'is_active' => rand(0, 1),
                'usuario_id' => $userIds[array_rand($userIds)],
                'equipo_id' => $equipoId,
                'recurso_id' => $recursoId,
            ];

            $reservations[] = $reservation;

            Reserva::create($reservation);
        }
    }
}
