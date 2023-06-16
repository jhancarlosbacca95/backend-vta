<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth{
    public $key;
    function __construct(){
        $this->key = 'clave_secreta_para_el_cifrado-99887766'; //Clave secreta para
    }

    public function signup($email,$password,$get_token=null){
        //buscar si existe el usuario por sus credenciales
        $user = User::where([
            'email'=>$email,
            'password' =>$password
        ])->first();

        //comprobar si se encontro el usuario
        $signup=false;
        if(is_object($user)){
            $signup=true;
        }

        //generar token con los datos del usuario identificado
        if($signup){
            $token = array(
                "sub" => $user->id,
                "name"=>$user->name,
                'email'=>$user->email,
                "surname"=>$user->surname,
                'description'=>$user->description,
                'image'=>$user->image,
                'role'=> $user->role,
                'iat'=> time(),
                'exp'=>time() + (24 * 7 * 60 * 60)
            );

            $jwt = JWT::encode($token,$this->key,'HS256');
            $decode = JWT::decode($jwt,$this->key,['HS256']);
            if(is_null($get_token)){
                $data = $jwt;
            }else{
                $data = $decode;
            }
        }else{
            $data=[
                'status'=>'error',
                'message'=>'El email o la contraseña son incorrectos.'
            ];
        }
        //Devolver los datos decodificados o el token en funcion de un parametro
        return $data;
    }

    public function checkToken($jwt,$getIdentity=false){
        $auth = false;

        try {
            $jwt = str_replace('"','',$jwt);
            $decoded = JWT::decode($jwt,$this->key,['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }
        if(!empty($decoded)&& is_object($decoded)&& isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }
        if($getIdentity){
            return $decoded;
        }
        return $auth;
    }
}
