<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoBeneficio extends Model
{
    protected $table = 'tipos_beneficios';
    public $timestamps=false;

    protected $fillable=[
        'descripcion',
        'categoria_id',
    ];

    public function categoriaBeneficio()
    {
        return $this->belongsTo(CategoriaBeneficio::class, 'categoria_id');
    }

    public function beneficio(){
            return $this->hasMany(Beneficio::class);
    }
}
