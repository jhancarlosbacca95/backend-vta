<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Stmt\Return_;

class NivelEducativo extends Model
{
    protected $table = 'niveles_educativos';
    public function propietarios(){
        return $this->belongsTo(Propietario::class,'id');
    }
}
