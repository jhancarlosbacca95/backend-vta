<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{

    public function __construct(){
        $this->middleware('api.auth',['except'=>['register','login']]);
    }
    
    public function register(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

            //Se limpian los espacios por delante y por detras 
            $params_array = array_map('trim', $params_array);

            //se validan los datos 
            $validate = \Validator::make($params_array, [
                'name' => 'required',
                'surname' => 'required',
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

    public function login(Request $request){

        $jwtAuth = new JwtAuth();

        $json = $request->input('json',null);
        $params= json_decode($json);
        $params_array = json_decode($json,true);

        $validate = \Validator::make($params_array,[
            'email'=>'required|email',
            'password'=> 'required'
        ]);

        if ($validate->fails()) {
            //validacion ha fallado
            $signup = array(
                'status' =>'error',
                'code'=>404,
                'message'=>'error con los datos ingresados',
                'errors'=>$validate->errors()
            );
        }else{
            //cifrar contraseña
            $pwd = hash('sha256',$params->password);

            //devolver el token
            $signup = $jwtAuth->signup($params->email,$pwd);

            if(!empty($params->gettoken)){
                $signup=$jwtAuth->signup($params->email,$pwd, true);
            }
        }
        return response()->json($signup , 200);
    }

    public function update(Request $request){
        //comprobar si el usuario esta identificado
      $token = $request->header('Authorization');
      $jwtAuth = new JwtAuth();
      $checkToken = $jwtAuth->checkToken($token);

      //recoger los datos por post
      $json = $request->input('json',null);
      $params_array = json_decode($json,true);
      if($checkToken && !empty($params_array)){
        //sacar el usuario identificado
        $user = $jwtAuth->checkToken($token,true);

        $validate = \Validator::make($params_array,[
            'name'=>'required',
            'surname'=>'required',
            'email'=>'required|email|unique:users' .$user->sub
        ]);

        //Quitar los campos que no se actualizaran
        unset($params_array['id']);
        unset($params_array['role']);
        unset($params_array['password']);
        unset($params_array['created_at']);
        unset($params_array['remember_token']);

        //Actualizar el usuario en la base de datos 
        $user_update = User::where('id',$user->sub)->update($params_array);

        //Devolver array con los resultados
        $data=[
            'code'=>200,
            'status'=>'success',
            'user'=>$user,
            'changes'=>$params_array
        ];

      }else{
        $data=[
            'code'=>400,
            'status'=>'error',
            'message'=>'el usuario no esta identificado'
        ];
      }
      return response()->json($data, $data['code']);
    }
    
    public function upload(Request $request){
        //Recoger los datos de la peticion 
        $image = $request->file('file0');
        $validate = \Validator::make($request->all(),[
            'file0'=>'required|mimes:jpg,jpeg,png,gif'
        ]);

        if(!$image || $validate->fails()){
            $data=[
                'code'=>400,
                'status'=>'error',
                'message'=>'Error al subir imagen'
            ];
        }else{
            //Se concatena la fecha con el nombre de la imagen
            $image_name = time().$image->getClientOriginalName();

            //lugar donde se guardara la imagen
            \Storage::disk('users')->put($image_name,\File::get($image));
        
            $data=[
                'code'=>200,
                'status'=>'success',
                'image'=>$image_name
            ];
        }
        return response()->json($data,$data['code']);
    }

    public function getImage($filename){
        $isset=\Storage::disk('users')->exists($filename);

        if($isset){
            $file = \Storage::disk('users')->get($filename);
            return new Response($file,200);
        }else{
            $data=[
                "code"=>404,
                'status'=>'error',
                'message'=>'Imagen no existe.'
            ];
            return response()->json($data,$data['code']);
        }
    }

}