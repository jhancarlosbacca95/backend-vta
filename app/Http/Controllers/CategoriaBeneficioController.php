<?php

namespace App\Http\Controllers;

use App\Models\CategoriaBeneficio;
use Illuminate\Http\Request;

class CategoriaBeneficioController extends Controller
{
    public function index()
    {
        $categorias = CategoriaBeneficio::all();
        $data = [
            'code' => 200,
            'status' => 'success',
            'categorias' => $categorias

        ];
        return response()->json($data, $data['code']);
    }

    public function show($id){
        $categoria = CategoriaBeneficio::find($id);
        if(is_null($categoria)){
            $data=[
                'code' => 404,
                'status' => 'Error',
                'message'=> 'No existe ninguna categoria con este id'
            ];
        }else{
            $data=[
                'code'=>200,
                'status'=>'success',
                'categoria'=>$categoria
            ];
        }
        return response()->json($data,$data['code']);
    }


    public function store(Request $request)
    {
        //primer se recogen los datos por post
        $json = $request->input('json'); //recibe la informacion del json 
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //Validan los datos
            $validate = \Validator::make($params_array, [
                'descripcion' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'Error',
                    'message' => 'Error con el campo de descripcion'
                ];
            } else {
                //Creamos la categoria 
                $categoria = new CategoriaBeneficio();
                $categoria->descripcion = $params_array['descripcion'];
                $categoria->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $categoria
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos correctos'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request)
    {
        //primer se recogen los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //Validan los datos
            $validate = \Validator::make($params_array, [
                'descripcion' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'Error',
                    'message' => 'Error con el campo de descripcion'
                ];
            } else {
                //Quitar los datos que no queremos actualizar
                
                unset($params_array['CodCategoriaB']);

                //Actualizar Categoria
                $categoria = CategoriaBeneficio::where('id', $id)->update($params_array);
                $category = CategoriaBeneficio::find($id);
                //devolver la categoria actualizada
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category,
                    'message' => 'Categoria Actualizada'
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se han enviado los datos correctos'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function destroy($id){
        $categoria = CategoriaBeneficio::find($id);
        if(is_null($categoria)){
            $data = [
                'code' => 400,
                'status' => 'Error',
                'message' => 'Categoria no encontrada'
            ];
        }else{
            $categoria->delete();
            $data = [
                'code'=>200,
                'status' => 'success',
                'message' => 'Categoria eliminada',
                'category' => $categoria
            ];
        }

        return response()->json($data,$data['code']);
        
    }
}