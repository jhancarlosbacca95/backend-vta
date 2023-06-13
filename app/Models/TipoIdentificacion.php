<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoIdentificacion extends Model
{
    protected $table = 'tipos_identificacion';

    protected $fillable=[
        'descripcion'
    ]; 

    public function propietario(){
    return $this->hasMany(Propietario::class, 'id');
}

    public function pariente(){
        return $this->hasMany(Pariente::class);
    }
}
