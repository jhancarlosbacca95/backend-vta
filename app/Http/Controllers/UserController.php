<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request){
        $json = $request->input('json',null);
        $params_array = json_decode($json,true);

        $validate = \Validator::make($params_array,[
            'name'=>'required|alpha',
            'surname'=>'required|alpha',
            'email' => 'required|unique:users|email'
            
        ]);
    }
}
