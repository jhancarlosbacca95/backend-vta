<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaBeneficio extends Model
{
    protected $table = 'categorias_beneficios';
    public $timestamps = false;
    

    public function tiposBeneficios(){
        return $this->hasMany(TipoBeneficio::class);
    }
}
