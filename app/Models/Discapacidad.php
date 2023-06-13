<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discapacidad extends Model
{
    protected $table = 'discapacidades';
    public $timestamps=false;

    protected $fillable=[
        'propietario_id',
        'escuchar',
        'hablar',
        'ver',
        'desplazarse',
        'agarrarMoverObjetos',
        'aprendizaje',
        'necesidadesBasicas',
        'relacionesSociales',
        'cardiacos',
        'otros',
    ];

    public function propietario()
{
    return $this->belongsTo(Propietario::class, 'propietario_id');
}
}
