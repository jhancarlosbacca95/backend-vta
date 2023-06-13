<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Carreta;
use App\Models\Discapacidad;
use App\Models\Pariente;
use App\Models\Propietario;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VtaController extends Controller
{
    public function index()
    {
        //Se realiza el select de todos los propietarios con la informacion relacionada a el
        $propietarios = Propietario::with([
            'animales',
            'carretas',
            'parientes',
            'discapacidades',
            'tipoIdentificacion',
            'etnia',
            'nivelEducativo',
            'tipoLugarVivienda',
            'municipio',
            'departamento'
        ])->get();

        //Se comprueba que existan propietarios en la base de datos
        if (
            $propietarios->count() === 0
        ) {
            $data = [
                'code' => 400,
                'status' => 'Error',
                'message' => 'No se encontraron datos en la base de datos'
            ];
        } else {
            $data = [
                'code' => 200,
                'status' => 'success',
                'propietarios' => $propietarios
            ];
        }

        //Se retorna $data
        return response()->json($data, $data['code']);
    }

    public function show($id)
    {
        // Se realiza la seleccion del propietario con la id recibida y todos los objetos relacionados a el 
        $propietario = Propietario::with([
            'animales',
            'carretas',
            'parientes',
            'discapacidades',
            'tipoIdentificacion',
            'etnia',
            'nivelEducativo',
            'tipoLugarVivienda',
            'municipio',
            'departamento'
        ])->find($id);

        // Se verifica si el propietario existe
        if ($propietario == null) {
            $data = [
                'code' => 400,
                'status' => 'Error',
                'message' => 'No se ha encontrado propietario con esa identificacion'
            ];
        } else {
            $data = [
                'code' => 200,
                'status' => 'success',
                'propietario' => $propietario
            ];
        }

        //Se retorna $data
        return response()->json($data, $data['code']);
    }

    public function store(Request $request)
    {

        //se reciben los datos del formulario para cada objeto a insertar
        $datos = $request->json()->all();
        $propietarioData = $datos['propietario'];
        $animalesData = $datos['animales'];
        $carretasData = $datos['carretas'];

        if (array_key_exists('parientes', $datos)) {
            $parientesData = $datos['parientes'];
        }

        if (array_key_exists('dificultadesPtes', $datos)) {
            $discapacidadesData = $datos['dificultadesPtes'];
        }

        //se reciben las validaciones de cada uno de los objetos que seran utilizadas en el Validator
        $propietarioRules = $this->validaciones()['propietario'];
        $animalesRules = $this->validaciones()['animales'];
        $carretasRules = $this->validaciones()['carretas'];
        $parientesRules = $this->validaciones()['parientes'];
        $discapacidadRules = $this->validaciones()['discapacidad'];

        //creo la variable data
        $data = [
            'code' => 400,
            'status' => 'error',
            'message' => 'Error al guardar datos'
        ];

        $propietario = Propietario::find($propietarioData['id']);
        if(empty($propietario)){
            try {
                //Se realizan las validaciones para los datos del propietario
                $validatedPropietarioData = \Validator::make($propietarioData, $propietarioRules)->validate();
    
                //Se realizan las validaciones para los datos de los animales
                $validatedAnimalesData = \Validator::make($animalesData, $animalesRules)->validate();
    
                //Se realizan las validaciones para los datos de las carretas
                $validatedCarretasData = \Validator::make($carretasData, $carretasRules)->validate();
    
                //Se realizan las validaciones para los datos de los parientes(en caso de que contenga datos)
                if (!empty($parientesData)) {
                    $validatedParientesData = \Validator::make($parientesData, $parientesRules)->validate();
                } else {
                    $validatedParientesData = [];
                }
    
                //Se realizan las validaciones para los datos de las discapacidades(en caso de que contenga datos )
                if (!empty($discapacidadesData)) {
                    $validatedDiscapacidadesData = \Validator::make($discapacidadesData, $discapacidadRules)->validate();
                } else {
                    $validatedDiscapacidadesData = [];
                }
    
                try {
                    //aqui se realizan las transacciones multiples a la base de datos
                    DB::transaction(function () use ($validatedPropietarioData, $validatedAnimalesData, $validatedCarretasData, &$data, $validatedParientesData, $validatedDiscapacidadesData) {
                        //insercion del propietario
                        $propietario = Propietario::create($validatedPropietarioData);
    
                        //Recorrido e insercion de los animales
                        foreach ($validatedAnimalesData as $animalData) {
                            $animal = Animal::create($animalData);
                        }
    
                        //Recorrido e insercion de las carretas
                        foreach ($validatedCarretasData as $carretaData) {
                            $carreta = Carreta::create($carretaData);
                        }
    
                        //Recorrido e insercion de los parientes
                        if (!empty($validatedParientesData)) {
                            foreach ($validatedParientesData as $parienteData) {
                                $pariente = Pariente::create($parienteData);
                            }
                        }
    
                        //Recorrido e insercion de las discapacidades
                        if (!empty($validatedDiscapacidadesData)) {
                            $dificultades = Discapacidad::create($validatedDiscapacidadesData);
                        }
                        //modificacion de los datos de respuesta en cuanto todo este ok
                        $data = [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Transaccion Exitosa',
                        ];
    
                    });
                } catch (QueryException $QE) {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'Ha ocurrido un error con la base de datos: ' . $QE->getMessage()
                    ];
                }
    
            } catch (\Illuminate\Validation\ValidationException $exception) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Ha ocurrido un error con la validacion de los datos: ' . $exception->getMessage()
                ];
            }
        }else{
            $data=[
                'code'=>400,
                'status'=>'error',
                'message'=>'La identificacion del propietario que intenta registrar ya se encuentra en el sistema'
            ];
        }

       

        return response()->json($data, $data['code']);
    }

    public function update(Request $request, $id)
    {
        // Se reciben los datos del formulario para cada objeto a actualizar
        $datos = $request->json()->all();
        $propietarioData = $datos['propietario'];
        $animalesData = $datos['animales'];
        $carretasData = $datos['carretas'];

        if (array_key_exists('parientes', $datos)) {
            $parientesData = $datos['parientes'];
        }

        if (array_key_exists('dificultadesPtes', $datos)) {
            $discapacidadesData = $datos['dificultadesPtes'];
        }

        // Se reciben las validaciones de cada uno de los objetos que serán utilizadas en el Validator
        $propietarioRules = $this->validaciones()['propietario'];
        $animalesRules = $this->validaciones()['animales'];
        $carretasRules = $this->validaciones()['carretas'];
        $parientesRules = $this->validaciones()['parientes'];
        $discapacidadRules = $this->validaciones()['discapacidad'];

        // Creo la variable data
        $data = [
            'code' => 400,
            'status' => 'error',
            'message' => 'Error al actualizar datos'
        ];

        try {
            // Se realizan las validaciones para los datos del propietario
            $validatedPropietarioData = \Validator::make($propietarioData, $propietarioRules)->validate();

            // Se realizan las validaciones para los datos de los animales
            $validatedAnimalesData = \Validator::make($animalesData, $animalesRules)->validate();

            // Se realizan las validaciones para los datos de las carretas
            $validatedCarretasData = \Validator::make($carretasData, $carretasRules)->validate();

            // Se realizan las validaciones para los datos de los parientes (en caso de que contenga datos)
            if (!empty($parientesData)) {
                $validatedParientesData = \Validator::make($parientesData, $parientesRules)->validate();
            } else {
                $validatedParientesData = [];
            }

            // Se realizan las validaciones para los datos de las discapacidades (en caso de que contenga datos)
            if (!empty($discapacidadesData)) {
                $validatedDiscapacidadesData = \Validator::make($discapacidadesData, $discapacidadRules)->validate();
            } else {
                $validatedDiscapacidadesData = [];
            }

            try {
                DB::transaction(function () use ($validatedPropietarioData, $validatedAnimalesData, $validatedCarretasData, &$data, $validatedParientesData, $validatedDiscapacidadesData, $id) {
                    $propietario = Propietario::findOrFail($id);
                    $propietario->update($validatedPropietarioData);
            
                    $propietario->animales()->delete();
                    $propietario->carretas()->delete();
                    $propietario->parientes()->delete();
                    $propietario->discapacidades()->delete();
            
                    foreach ($validatedAnimalesData as $animalData) {
                        $animal = $propietario->animales()->create($animalData);
                    }
                    
                    foreach ($validatedCarretasData as $carretaData) {
                        $carreta = $propietario->carretas()->create($carretaData);
                    }
                    
                    if (!empty($validatedParientesData)) {
                        foreach ($validatedParientesData as $parienteData) {
                            $pariente = $propietario->parientes()->create($parienteData);
                        }
                    }
                    
                    if (!empty($validatedDiscapacidadesData)) {
                        $discapacidad = $propietario->discapacidades()->create($validatedDiscapacidadesData);
                    }
            
                    // Aquí puedes hacer lo que necesites con los IDs obtenidos
            
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Datos actualizados correctamente',
                    ];
                });
            } catch (QueryException $QE) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Ha ocurrido un error con la base de datos: ' . $QE->getMessage()
                ];
            }
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Ha ocurrido un error con la validación de los datos: ' . $exception->getMessage()
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id){
        $propietario = Propietario::find($id);
        if (empty($propietario)) {
            $data=[
                'code' => 404,
                'state'=>'error',
                'message'=>'no existe propietario con esa identificacion'
            ];
        }else{
            $propietario->delete();
            $data=[
                'code' => 200,
                'state'=>'success',
                'message'=>'propietario eliminado correctamente'
            ];
        }

        return response()->json($data, $data['code']);
    }


    public function validaciones()
    {
        $propietarioRules = [
            'tipo_identificacion_id' => 'required',
            'id' => 'required|numeric',
            'primerNombre' => 'required',
            'segundoNombre' => 'nullable',
            'primerApellido' => 'required',
            'segundoApellido' => 'nullable',
            'sexo' => 'required',
            'direcEntrevista' => 'required',
            'fechaEntrevista' => 'required',
            'fechaNacimiento' => 'required',
            'edadCumplida' => 'required|numeric',
            'direccionVivHabitual' => 'required',
            'tipoLugarVivienda_id' => 'required',
            'depResidenciaHabitual_id' => 'required',
            'munResidenciaHabitual_id' => 'required',
            'telefono1' => 'nullable',
            'telefono2' => 'nullable',
            'celular1' => 'required_with:celular2|numeric',
            'celular2' => 'required_with:celular1|numeric',
            'correoE1' => 'required_with:correoE2|email',
            'correoE2' => 'required_with:correoE1|email',
            'regPropVta' => 'required',
            'sabeFechaRegPropVta' => 'required_if:regPropVta,"Si"',
            'fechaRegPropVta' => 'required_if:sabeFechaRegPropVta,"Si"',
            'etnia_id' => 'required',
            'dificultadesPtes' => 'required',
            'sabeLeerEscribir' => 'required',
            'nivelEducativo_id' => 'required',
            'laborA' => 'required',
            'laborM' => 'required',
            'barrioPrincipalLabor' => 'required',
            'pgirs' => 'required',
            'asociacion' => 'required',
            'nombreAsociacion' => 'required_if:asociacion,"Si"',
            'ingresosMensualesProm' => 'required',
            'licenciaVigente' => 'required',
            'personasDependen' => 'required',
            'laborDeseada' => 'required'
        ];

        $animalesRules = [
            '*.propietario_id' => 'required',
            '*.tipoAnimal' => 'required',
            '*.nombre' => 'required',
            '*.edad' => 'required|numeric',
            '*.sexo' => 'required',
            '*.raza' => 'required',
            '*.colorCuerpo' => 'required',
            '*.colorCrin' => 'required',
            '*.tieneCertificado' => 'required',
            '*.diaCertificado' => 'required_if:*.tieneCertificado,"Si"',
            '*.mesCertificado' => 'required_if:*.tieneCertificado,"Si"',
            '*.anioCertificado' => 'required_if:*.tieneCertificado,"Si"',
            '*.estadoFisico' => 'required',
            '*.tieneID' => 'required',
            '*.numeroID' => 'required_if:*.tieneID,"Si"',
            '*.enPosesion' => 'required',
            '*.descripcion' => 'required_if:*.enPosesion,"No"',
        ];


        $carretasRules = [
            '*.propietario_id' => 'required',
            '*.tipoCarreta' => 'required',
            '*.materialPred' => 'required',
            '*.estado' => 'required',
            '*.tieneIdPlaca' => 'required',
            '*.numIdPlaca' => 'required_if:*.tieneIdPlaca,"Si"',
            '*.pesoCargaProm' => 'required',
            '*.tipoCarga' => 'required',
            '*.otroCual' => 'required_if:*.tipoCarga,"Otro"'
        ];

        $discapacidadRules = [
            'propietario_id' => 'required',
            'escuchar' => 'required',
            'hablar' => 'required',
            'ver' => 'required',
            'desplazarse' => 'required',
            'agarrarMoverObjetos' => 'required',
            'aprendizaje' => 'required',
            'necesidadesBasicas' => 'required',
            'relacionesSociales' => 'required',
            'cardiacos' => 'required',
            'otros' => 'nullable'
        ];

        $parientesRules = [
            '*.tipo_identificacion_id' => 'required',
            '*.id' => 'required',
            '*.propietario_id' => 'required',
            '*.nombreCompleto' => 'required',
            '*.edad' => 'required',
            '*.sexo' => 'required',
            '*.parentezco' => 'required',
            '*.estadoCivil' => 'required',
            '*.laborVta' => 'required'
        ];
        return [
            'propietario' => $propietarioRules,
            'animales' => $animalesRules,
            'carretas' => $carretasRules,
            'discapacidad' => $discapacidadRules,
            'parientes' => $parientesRules,
        ];
    }
}