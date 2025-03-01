<?php

namespace Database\Seeders;

use App\Models\Equipo;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReservaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();
        $equipoIds = Equipo::pluck('id')->toArray();
        $recursoIds = Recurso::pluck('id')->toArray();

        $reservations = [];

        for ($i = 0; $i < 20; $i++) {
            do {
            $startHour = rand(8, 16); // Start hour between 8 AM and 4 PM
            $startMinute = rand(0, 3) * 45; // Start minute at 0, 45
            $startDate = Carbon::now()->setTime($startHour, $startMinute, 0);
            $endDate = (clone $startDate)->addMinutes(45); // Duration is 45 minutes

            // Ensure end time does not exceed 5:45 PM
            if ($endDate->hour > 17 || ($endDate->hour == 17 && $endDate->minute > 45)) {
                $endDate->setTime(17, 45, 0);
            }

            // Ensure start time is between 8:00 AM and 5:00 PM
            if ($startDate->hour == 8 && $startDate->minute < 0) {
                $startDate->setTime(8, 0, 0);
            } elseif ($startDate->hour == 17 && $startDate->minute > 0) {
                $startDate->setTime(17, 0, 0);
            }

            $overlap = false;
            foreach ($reservations as $reservation) {
                if ($startDate->between($reservation['hora_inicio'], $reservation['hora_fin']) ||
                $endDate->between($reservation['hora_inicio'], $reservation['hora_fin'])) {
                $overlap = true;
                break;
                }
            }
            } while ($overlap);

            // Ensure either equipo_id or recurso_id is set, but not both null
            $equipoId = rand(0, 1) ? $equipoIds[array_rand($equipoIds)] : null;
            $recursoId = is_null($equipoId) ? $recursoIds[array_rand($recursoIds)] : null;

            $reservation = [
            'hora_inicio' => $startDate,
            'hora_fin' => $endDate,
            'estado' => ['Por aprobar', 'Aprobada', 'No aprobada', 'Finalizada'][rand(0, 3)],
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
