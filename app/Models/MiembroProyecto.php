<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiembroProyecto extends Model
{
    use HasFactory;

    // Campos que se pueden asignar de forma masiva (al crear o actualizar un registro).
    protected $fillable = [
        'miembro_id',
        'proyecto_id',
        'is_active',
    ];

    // Un miembro de proyecto pertenece a un usuario.
    public function miembro()
    {
        return $this->belongsTo(User::class);
    }


    // Un miembro de proyecto pertenece a un proyecto.
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
}
