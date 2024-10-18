<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    // Campos que se pueden asignar de forma masiva (al crear o actualizar un registro).
    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'is_active',
    ];

    // Un proyecto puede tener muchos miembros.
    public function miembros()
    {
        return $this->hasMany(MiembroProyecto::class);
    }

    // Un proyecto puede tener muchas asistencias.
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}
