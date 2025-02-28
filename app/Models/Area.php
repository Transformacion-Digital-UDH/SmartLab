<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    // Los campos que pueden ser asignados de manera masiva (al crear o actualizar un registro).
    protected $fillable = [
        'nombre',
        'descripcion',
        'aforo',
        'is_active',
        'tipo',
        'laboratorio_id',
    ];

    // Un área pertenece a un laboratorio.
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class);
    }

    // Un área puede tener muchos recursos.
    public function recursos()
    {
        return $this->hasMany(Recurso::class);
    }

    // Un área puede tener muchos equipos.
    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    // Un área puede tener varias fotos
    public function fotos()
    {
        return $this->hasMany(FotoArea::class);
    }

}
