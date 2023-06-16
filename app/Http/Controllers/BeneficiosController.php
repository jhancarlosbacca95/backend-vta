<?php

namespace App\Http\Controllers;

use App\Models\Beneficio;
use Illuminate\Http\Request;

class BeneficiosController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth');
    }
    public function index(){
    $beneficios = Beneficio::all()->load('tipoBeneficio.categoriaBeneficio');

    if(!empty($beneficios)){
        $data = [
            'code'=>200,
            'status'=>'success',
            'beneficios'=>$beneficios
        ];
    }else{
        $data = [
            'code'=>404,
            'status'=>'error',
            'message'=>'no existen beneficios asignados para mostrar'
        ];
    }
    return response()->json($data,$data['code']);
    }

    public function show($id){
        $beneficio = Beneficio::with(
            'tipoBeneficio.categoriaBeneficio' 
          )->find($id);
        if(!empty($beneficio)){
            $data = [
                'code'=>200,
                'status'=>'success',
                'beneficio'=>$beneficio
            ];
        }else{
            $data = [   
                'code'=>404,
                'status'=>'error',
                'message'=>'No se encontro ningun beneficio con ese id'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $request){
    $json = $request->input('json',null);
    $params_array = json_decode($json,true);


    if(!empty($params_array)){

    $validate = \Validator::make($params_array, [
        'propietario_id'=>'required',
        'fecha'=>'required',
        'fechaEntrega'=>'required',
        'tipoBeneficio_id'=>'required',
        'descripcion'=>'required'
        ]);

        if($validate->fails()){
            $data=[
                'code'=>400,
                'status'=>'error',
                'message'=>'error con los datos ingresados'
            ];
        }else{
            $beneficio=Beneficio::create($params_array);
            $data = [
                'code'=>200,
                'status'=>'success',
                'beneficio'=>$beneficio
            ];
        }
    }else{
        $data=[
            'code'=>400,
            'status'=>'error',
            'message'=>'no se han encontrado datos para ingresar'
        ];
    }

    return response()->json($data,$data['code']);
    }

    public function update($id,Request $request){
        $json = $request->input('json',null);
        $params_array = json_decode($json,true);

        $validate = \Validator::make($params_array, [
            'propietario_id'=>'required',
            'fecha'=>'required',
            'fechaEntrega'=>'required',
            'tipoBeneficio_id'=>'required',
            'descripcion'=>'required']);

        if($validate->fails()){
            $data=[
                'code'=>400,
                'status'=>'error',
                'message'=>'Error con los datos'
            ];
        }else{
            $beneficio = Beneficio::find($id);
            if(empty($beneficio)){
                $data = [
                    'code'=>404,
                    'status'=>'error',
                    'message'=>'beneficio no existe'
                ];
            }else{
                //eliminar los datos que no se actualizarán
                isset($params_array['id']);
                isset($params_array['propietario_id']);

                $beneficio->update($params_array);

                $data=[
                    'code'=>200,
                    'status'=>'success',
                    'message'=>'se han actualizados los datos'
                ];
                    
            }
        }
        return response()->json($data, $data['code']);
    }

    public function destroy($id){
        $beneficio = Beneficio::find($id);

        if(!$beneficio){
            $data=[
                'code'=>404,
                'status'=>'error',
                'message'=>'no se encontro el beneficio'
            ];
        }else{
            $beneficio->delete();
            $data=[
                'code'=>200,
                'status'=>'success',
                'message'=>'se ha eliminado el beneficio'
            ];
        }
        return response()->json($data, $data['code']);
    }
}
