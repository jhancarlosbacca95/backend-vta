<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pariente extends Model
{
    protected $table = 'parientes';
    public $timestamps=false;

    protected $fillable=[
        'tipo_identificacion_id',
        'id',
        'propietario_id',
        'nombreCompleto',
        'edad',
        'sexo',
        'parentezco',
        'estadoCivil',
        'laborVta'
            ];

    public function propietario(){
        return $this->belongsTo(Propietario::class,'propietario_id');
    }
}
