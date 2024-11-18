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
        'estado',
        'is_active',
        'responsable_id',
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

    // Un proyecto puede tener muchos recursos.
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

}
