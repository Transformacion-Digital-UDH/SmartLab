<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombres',
        'apellidos',
        'dni',
        'email',
        'celular',
        'password',
        'codigo',
        'celular',
        'rol',
        'is_active',
        'google_token',
        'google_refresh_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Un usuario puede tener muchas asistencias.
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'usuario_id');
    }

    // Un usuario puede tener muchas reservas.
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    // Un usuario puede participar en muchos proyectos.
    public function miembroProyectos()
    {
        return $this->hasMany(MiembroProyecto::class);
    }

    public function laboratoriosResponsable()
    {
        return $this->hasMany(Laboratorio::class, 'responsable_id');
    }

    public function laboratoriosCoordinador()
    {
        return $this->hasMany(Laboratorio::class, 'coordinador_id');
    }

    // Setters
    public function setDniAttribute($value)
    {
        $this->attributes['dni'] = trim($value);
    }

    public function setCelularAttribute($value)
    {
        $this->attributes['celular'] = trim($value);
    }

    public function setCodigoAttribute($value)
    {
        $this->attributes['codigo'] = trim($value);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = mb_strtolower(trim($value), 'UTF-8');
    }

    public function setNombresAttribute($value)
    {
        $this->attributes['nombres'] = mb_strtoupper(trim($value), 'UTF-8');
    }

    public function setApellidosAttribute($value)
    {
        $this->attributes['apellidos'] = mb_strtoupper(trim($value), 'UTF-8');
    }
}
