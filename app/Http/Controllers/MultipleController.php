<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Etnia;
use App\Models\Municipio;
use App\Models\NivelEducativo;
use App\Models\TipoIdentificacion;
use App\Models\TipoLugarVivienda;
use Illuminate\Http\Request;

class MultipleController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth');
    }
    public function municipios(){
        $municipios = Municipio::all()->load('departamento');

        $data=[
            'code'=>200,
            'status' => 'success',
            'municipios' =>$municipios
        ];

        return response()->json($data, $data['code']);
    }

    public function municipiosxDepto($id){
        $municipios = Municipio::where('departamento_id', $id)->get();
        $municipios->load('departamento');
    
        $data=[
            'code'=>200,
            'status' => 'success',
            'municipios' =>$municipios
        ];

        return response()->json($data, $data['code']);
    }


    public function departamentos(){
        $departamentos = Departamento::all();

        $data=[
            'code'=>200,
            'status' => 'success',
            'departamentos' =>$departamentos
            ];
            return response()->json($data, $data['code']);
    }

    public function etnias(){
        $etnias = Etnia::all();
        $data=[
            'code'=>200,
            'status' => 'success',
            'etnias' =>$etnias
            ];
            return response()->json($data, $data['code']);
    }

    public function nivelesEducativos(){
        $niveles = NivelEducativo::all();
        $data=[
            'code'=>200,
            'status' => 'success',
            'niveles' =>$niveles
            ];
            return response()->json($data, $data['code']);
    }

    public function tiposIdentificacion(){
            $tipos = TipoIdentificacion::all();
            $data=[
                'code'=>200,
                'status' => 'success',
                'tipos' =>$tipos
                ];
                return response()->json($data, $data['code']);
    }
    
    public function tiposLugarVivienda(){
        $tiposLugarVivienda = TipoLugarVivienda::all();
        $data=[
            'code'=>200,
            'status' => 'success',
            'tipos' =>$tiposLugarVivienda
            ];
            return response()->json($data, $data['code']);
    }

}
