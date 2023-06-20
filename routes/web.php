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

//rutas de las categorias y tipos de beneficios
Route::resource('/api/categorias','App\Http\Controllers\CategoriaBeneficioController');
Route::resource('/api/tipoBeneficio','App\Http\Controllers\TipoBeneficioController');

//rutas de los vta(y todos sus componentes)
Route::resource('/api/vta','App\Http\Controllers\VtaController');

//Rutas de los beneficios(asignaciones etc)
Route::resource('/api/beneficios','App\Http\Controllers\BeneficiosController');

//ruta de los usuarios
Route::post('api/registro','App\Http\Controllers\UserController@register');
Route::post('api/login','App\Http\Controllers\UserController@login');
Route::put('/api/user/update', 'App\Http\Controllers\UserController@update');
Route::post('/api/user/upload','App\Http\Controllers\UserController@upload');
Route::get('/api/user/avatar/{filename}','App\Http\Controllers\UserController@getImage');

//Ruta de los pdfs
Route::post('/api/pdf/upload','App\Http\Controllers\PdfController@upload');
Route::get('/api/pdf/getPdf/{filename}','App\Http\Controllers\PdfController@getPdf');
Route::get('/api/pdf/generate/{id}','App\Http\Controllers\PdfController@generate');

//rutas para el controllador multiple
Route::get('/api/municipios','App\Http\Controllers\MultipleController@municipios');
Route::get('/api/municipios/{id}','App\Http\Controllers\MultipleController@municipiosxDepto');
Route::get('/api/departamentos','App\Http\Controllers\MultipleController@departamentos');
Route::get('/api/etnias','App\Http\Controllers\MultipleController@etnias');
Route::get('/api/nivelesEducativos','App\Http\Controllers\MultipleController@nivelesEducativos');
Route::get('/api/tiposIdentificacion','App\Http\Controllers\MultipleController@tiposIdentificacion');
Route::get('/api/tiposLugarVivienda','App\Http\Controllers\MultipleController@tiposLugarVivienda');