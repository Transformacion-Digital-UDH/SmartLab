<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoArea extends Model
{
    use HasFactory;
<<<<<<< HEAD
    protected $table = 'fotos_areas';
=======

    protected $table = 'fotos_areas';

>>>>>>> dcbf80d3a65200e8ac9d17a43dba829de91c12ab
    protected $fillable = [
        'area_id',
        'ruta',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
