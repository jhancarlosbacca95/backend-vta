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
    }
}
