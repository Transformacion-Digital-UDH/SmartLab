<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoEquipo extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruta',
        'equipo_id',
    ];

    /**
     * Relación con el modelo Equipo.
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}
