<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Laboratorio extends Model
{
    use HasFactory;

    protected $appends = ['responsable', 'coordinador'];


    // Campos que se pueden asignar de forma masiva (al crear o actualizar un registro).
    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'aforo',
        'email',
        'inauguracion',
        'ubicacion',
        'google_calendar_id',
        'is_active',
    ];

    // Relaciones

    /**
     * Un laboratorio tiene muchos proyectos.
     */
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }

    // Un laboratorio puede tener muchas áreas.
    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    // Un laboratorio puede tener muchas asistencias.
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'laboratorio_id');
    }

    // Relación muchos a muchos con usuarios (participantes del laboratorio).
    public function participantes()
    {
        return $this->belongsToMany(User::class, 'laboratorio_user')
                    ->withPivot('rol')
                    ->using(LaboratorioUser::class);
    }

    public function miembros()
    {
        return $this->belongsToMany(User::class, 'laboratorio_user')
                    ->withPivot('rol')
                    ->wherePivot('rol', 'Miembro')
                    ->using(LaboratorioUser::class);
    }

    public function responsable()
    {
        // Devuelve el primer responsable encontrado
        return $this->belongsToMany(User::class, 'laboratorio_user')
                    ->withPivot('rol')
                    ->wherePivot('rol', 'Responsable')
                    ->using(LaboratorioUser::class)
                    ->oldest('laboratorio_user.created_at')  // Ordena por el más antiguo primero
                    ->limit(1);
    }

    public function getResponsableAttribute()
    {
        return $this->responsable()->first();
    }

    public function coordinador()
    {
        // Devuelve el primer coordinador encontrado
        return $this->belongsToMany(User::class, 'laboratorio_user')
                    ->withPivot('rol')
                    ->wherePivot('rol', 'Coordinador')
                    ->using(LaboratorioUser::class)
                    ->oldest('laboratorio_user.created_at')  // Ordena por el más antiguo primero
                    ->limit(1);
    }

    public function getCoordinadorAttribute()
    {
        return $this->coordinador()->first();
    }

}
