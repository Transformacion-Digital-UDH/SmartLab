<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    // Campos que se pueden asignar de forma masiva (al crear o actualizar un registro).
    protected $fillable = [
        'hora_inicio',
        'hora_fin',
        'estado',
        'google_event_id',
        'is_active',
        'usuario_id',
        'equipo_id',
        'recurso_id',
        'area_id',

    ];

    // Una reserva pertenece a un usuario.
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    // Una reserva puede estar asociada a un equipo (opcional).
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    // Una reserva puede estar asociada a un recurso (opcional).
    public function recurso()
    {
        return $this->belongsTo(Recurso::class);
    }

    // Una reserva puede estar asociada a un área (opcional).
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
