<?php

namespace App\Http\Controllers;

use App\Models\TipoBeneficio;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TipoBeneficioController extends Controller
{
    public function index()
    {
        $tipoBene = TipoBeneficio::all()->load('categoriaBeneficio');
        if ($tipoBene->isEmpty()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se encontraron tipos de beneficio'
            ];
        } else {
            $data = [
                'code' => 200,
                'status' => 'success',
                'tipoBeneficio' => $tipoBene
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function show($id)
    {
        try {
            $tipoBene = TipoBeneficio::findOrFail($id)->load('categoriaBeneficio');
            $data = [
                'code' => 200,
                'status' => 'success',
                'tipoBeneficio' => $tipoBene
            ];
        } catch (ModelNotFoundException $e) {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No se encontro el tipo de beneficio'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $request)
    {
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'descripcion' => 'required',
                'categoria_id' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'messagge' => 'problema con los datos'
                ];
            } else {
                $tipoBene = new TipoBeneficio();
                $tipoBene->descripcion = $params->descripcion;
                $tipoBene->categoria_id = $params->categoria_id;

                $tipoBene->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'tipoBeneficio' => $tipoBene
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'debe ingresar los datos'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //validar los datos
            $validate = \Validator::make($params_array, [
                'descripcion' => 'required',
                'categoria_id' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Error con los datos enviados'
                ];
            } else {
                //quitar los campos que no se van a modificar
                isset($params_array['id']);

                // buscar el elemento a modificar
                $tipoBene = TipoBeneficio::where('id', $id)->first();
                if (!empty($tipoBene)) {
                    $tipoBene->update($params_array);
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'tipoBeneficio' => $tipoBene
                    ];
                }else{
                    $data = [  
                        'code' => 400,
                        'status' =>'error',
                        'message'=>'no se encontro tipo de beneficio'
                        ];
                }
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'debe ingresar los datos'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function destroy($id)
    {
        $tipoBene = TipoBeneficio::where('id', $id)->first();
        if (!empty($tipoBene)) {
            $tipoBene->delete();
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Tipo de beneficio eliminado'
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Tipo de beneficio no encontrado'
            ];
        }
        return response()->json($data, $data['code']);

    }

}