<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{

 //tabla referencia en la base de datos
    protected $table ='animales';
    public $timestamps = false;

    protected $fillable=[
        'propietario_id',
        'tipoAnimal',
        'nombre',
        'edad',
        'sexo',
        'raza',
        'colorCuerpo',
        'colorCrin',
        'tieneCertificado',
        'diaCertificado',
        'mesCertificado',
        'anioCertificado',
        'estadoFisico',
        'tieneID',
        'numeroID',
        'enPosesion',
        'descripcion',
    ];

    //relacion con los propietarios en la base de datos
    public function propietario(){
        return $this->belongsTo(Propietario::class,'propietario_id');
    }
    
}
