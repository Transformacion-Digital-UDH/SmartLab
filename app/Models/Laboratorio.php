<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratorio extends Model
{
    use HasFactory;

    // Campos que se pueden asignar de forma masiva (al crear o actualizar un registro).
    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'aforo',
        'email',
        'inauguracion',
        'is_active',
        'responsable_id',
        'coordinador_id',
    ];

    // Relaciones

    // Un laboratorio puede tener muchas áreas.
    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    // Un laboratorio tiene un responsable que es un usuario.
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    // Un laboratorio tiene un coordinador que es un usuario.
    public function coordinador()
    {
        return $this->belongsTo(User::class, 'coordinador_id');
    }

    // Un laboratorio puede tener muchas asistencias.
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'laboratorio_id');
    }
}
