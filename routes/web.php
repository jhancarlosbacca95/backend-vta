<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::resource('/api/categorias','App\Http\Controllers\CategoriaBeneficioController');
Route::resource('/api/tipoBeneficio','App\Http\Controllers\TipoBeneficioController');

Route::resource('/api/vta','App\Http\Controllers\VtaController');

Route::resource('/api/beneficios','App\Http\Controllers\BeneficiosController');


//rutas para el controllador multiple
Route::get('/api/municipios','App\Http\Controllers\MultipleController@municipios');
Route::get('/api/municipios/{id}','App\Http\Controllers\MultipleController@municipiosxDepto');
Route::get('/api/departamentos','App\Http\Controllers\MultipleController@departamentos');
Route::get('/api/etnias','App\Http\Controllers\MultipleController@etnias');
Route::get('/api/nivelesEducativos','App\Http\Controllers\MultipleController@nivelesEducativos');
Route::get('/api/tiposIdentificacion','App\Http\Controllers\MultipleController@tiposIdentificacion');
Route::get('/api/tiposLugarVivienda','App\Http\Controllers\MultipleController@tiposLugarVivienda');