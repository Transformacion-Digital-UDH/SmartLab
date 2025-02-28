<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model 
{
    use HasFactory;

    // Campos que se pueden asignar de forma masiva (al crear o actualizar un registro).
    protected $fillable = [
        'hora_entrada',
        'hora_salida',
        'tarea',
        'nota',
        'is_active',
        'usuario_id',
        'proyecto_id',
        'laboratorio_id',
    ];

    // Una asistencia pertenece a un usuario.
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    // Una asistencia puede estar asociada a un proyecto (opcional).
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    // Una asistencia pertenece a un laboratorio.
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'laboratorio_id');
    }

    // Una asistencia puede tener muchos usos (relacionados con recursos).
    public function usos()
    {
        return $this->hasMany(Uso::class);
    }
}
