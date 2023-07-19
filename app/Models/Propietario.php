<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propietario extends Model
{
    protected $table = 'propietarios';
    public $timestamps = false;

    protected $fillable=[
        'codEncuesta',
        'tipo_identificacion_id',
        'id',
        'primerNombre',
        'segundoNombre',
        'primerApellido',
        'segundoApellido',
        'sexo',
        'direcEntrevista',
        'fechaEntrevista',
        'fechaNacimiento',
        'edadCumplida',
        'direccionVivHabitual',
        'tipoLugarVivienda_id',
        'depResidenciaHabitual_id',
        'munResidenciaHabitual_id',
        'telefono1',
        'telefono2',
        'celular1',
        'celular2',
        'correoE1',
        'correoE2',
        'regPropVta',
        'sabeFechaRegPropVta',
        'fechaRegPropVta',
        'etnia_id',
        'dificultadesPtes',
        'sabeLeerEscribir',
        'nivelEducativo_id',
        'laborA',
        'laborM',
        'barrioPrincipalLabor',
        'pgirs',
        'asociacion',
        'nombreAsociacion',
        'ingresosMensualesProm',
        'licenciaVigente',
        'personasDependen',
        'laborDeseada'
    ];

    public function animales(){
        return $this->hasMany(Animal::class);
    }

    public function carretas(){
        return $this->hasMany(Carreta::class);
    }

    public function discapacidades(){
        return $this->hasOne(Discapacidad::class);
    }

    public function parientes(){
        return $this->hasMany(Pariente::class);
    }

    public function beneficio(){
        return $this->hasOne(Beneficio::class);
    }

    public function pdf(){
        return $this->hasOne(Pdf::class);
    }

    //metodos para traer la informacion de las llaves foraneas 
    public function tipoIdentificacion(){
        return $this->belongsTo(TipoIdentificacion::class,'tipo_identificacion_id');
    }

    public function etnia(){
        return $this->belongsTo(Etnia::class,'etnia_id');
    }

    public function nivelEducativo(){
        return $this->belongsTo(NivelEducativo::class,'nivelEducativo_id');
    }

    public function tipoLugarVivienda(){
        return $this->belongsTo(TipoLugarVivienda::class,'tipoLugarVivienda_id');
    }
    
    public function municipio(){
        return $this->belongsTo(Municipio::class,'munResidenciaHabitual_id');
    }

    public function departamento(){
        return $this->belongsTo(Departamento::class,'depResidenciaHabitual_id');
    }

}
