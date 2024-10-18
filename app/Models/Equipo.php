<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    // Campos que se pueden asignar de forma masiva (al crear o actualizar un registro).
    protected $fillable = [
        'nombre',
        'codigo',
        'tipo',
        'descripcion',
        'estado',
        'is_active',
        'area_id',
    ];

    // Un equipo pertenece a un área.
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    // Un equipo puede tener muchos recursos.
    public function recursos()
    {
        return $this->hasMany(Recurso::class);
    }

    // Un equipo puede tener muchas reservas.
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}
