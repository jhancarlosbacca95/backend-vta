<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoLugarVivienda extends Model
{
    protected $table = 'tipos_lugar_vivienda';

    public function propietario(){
        return $this->hasMany(Propietario::class);
    }
}
