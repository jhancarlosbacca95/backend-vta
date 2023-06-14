<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

            //Se limpian los espacios por delante y por detras 
            $params_array = array_map('trim', $params_array);

            //se validan los datos 
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|unique:users|email',
                'password' => 'required'
            ]);

            if ($validate->fails()) {
                //la validacion falló
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'no se ha creado el usuario error con los datos',
                    'error' => $validate->errors()
                ];
            } else {
                //validacion exitosa

                //cifrar la contraseña

                $pwd = hash('sha256', $params_array['password']);

                //crear el usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                //Guardar en la base de datos
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'messaje' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );


            }
        }else{
            $data =[
                "status" =>"error",
                "code"=>400,
                "message"=>"debe ingresar los datos"
            ];
        }

        return response()->json($data,$data['code']);
    }
    
}