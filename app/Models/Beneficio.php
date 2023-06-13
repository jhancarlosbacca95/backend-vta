<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficio extends Model
{
    //tabla referenciada en la base de datos
    protected $table = 'beneficios';

    public $timestamps = false;

    public $fillable=[
        'propietario_id',
        'fecha',
        'fechaEntrega',
        'tipoBeneficio_id',
        'descripcion'
    ];

    public function  tipoBeneficio(){
        return $this->belongsTo(TipoBeneficio::class,'tipoBeneficio_id');
    }

    public function propietario(){
        return $this->belongsTo(Propietario::class,'propietario_id');
    }

    public function categoriaBeneficio()
    {
        return $this->tipoBeneficio()->belongsTo(CategoriaBeneficio::class, 'categoriaBeneficio_id');
    }
}
