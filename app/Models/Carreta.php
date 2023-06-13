<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carreta extends Model
{
    protected $table = 'carretas';

    public $timestamps=false;

    protected $fillable=[
        'propietario_id',
        'tipoCarreta',
        'materialPred',
        'estado',
        'tieneIdPlaca',
        'numIdPlaca',
        'pesoCargaProm',
        'tipoCarga',
        'otroCual'
    ];

    public function propietario(){
        return $this->belongsTo(Propietario::class,'propietario_id');
    }
}
