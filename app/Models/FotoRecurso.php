<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoRecurso extends Model
{
    use HasFactory;

    protected $table = 'fotos_recursos';

    protected $fillable = [
        'ruta',
        'recurso_id',
    ];

    /**
     * Relación con el recurso.
     */
    public function recurso()
    {
        return $this->belongsTo(Recurso::class);
    }
}
