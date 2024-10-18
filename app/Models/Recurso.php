<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recurso extends Model
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
        'equipo_id',
    ];

    // Un recurso pertenece a un área.
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    // Un recurso puede pertenecer a un equipo (opcional).
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    // Un recurso puede tener muchas características.
    public function caracteristicas()
    {
        return $this->hasMany(Caracteristica::class);
    }

    // Un recurso puede estar relacionado con muchos usos.
    public function usos()
    {
        return $this->hasMany(Uso::class);
    }
}
