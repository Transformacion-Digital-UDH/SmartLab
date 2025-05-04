<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaboratorioUser extends Pivot
{
    use HasFactory;

    protected $table = 'laboratorio_user';

    protected $fillable = [
        'user_id',
        'laboratorio_id',
        'rol',
    ];

 
    public function user()
    {
        return $this->belongsTo(User::class);
    }

 
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class);
    }
}