<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caracteristica extends Model
{
    use HasFactory;

    // Campos que se pueden asignar de forma masiva (al crear o actualizar un registro).
    protected $fillable = [
        'clave',
        'valor',
        'is_active',
        'recurso_id',
    ];

    // Una característica pertenece a un recurso.
    public function recurso()
    {
        return $this->belongsTo(Recurso::class);
    }
}
