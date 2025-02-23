<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'ruta',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
