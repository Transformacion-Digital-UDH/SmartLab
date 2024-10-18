<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uso extends Model
{
    use HasFactory;

    // Campos que se pueden asignar de forma masiva (al crear o actualizar un registro).
    protected $fillable = [
        'hora_entrada',
        'hora_salida',
        'is_active',
        'recurso_id',
        'asistencia_id',
    ];

    // Un uso pertenece a un recurso.
    public function recurso()
    {
        return $this->belongsTo(Recurso::class);
    }

    // Un uso pertenece a una asistencia.
    public function asistencia()
    {
        return $this->belongsTo(Asistencia::class);
    }
}
