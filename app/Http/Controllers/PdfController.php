<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PdfController extends Controller
{

    public function __construct(){
        $this->middleware('api.auth',['except'=>['getPdf']]);
    }
    public function generate()
    {

    }


    public function upload(Request $request)
    {
        //recoger los datos de la peticion 
        $pdf = $request->file('pdf');

        //Validar el tipo de dato enviado
        $validate = \Validator::make($request->all(), [
            'pdf' => 'required|mimes:pdf'
        ]);

        if (!$pdf || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Eror al intentar subir el archivo, revise el tipo de archivo sea pdf'
            ];
        } else {
            //Se concatena el tiempo con el nombre del archivo para que no se repitan
            $name_file = time() . $pdf->getClientOriginalName();

            //lugar donde se almacenara el pdf
            \Storage::disk('pdfs')->put($name_file, \File::get($pdf));

            $data = [
                'code' => 200,
                'status' => 'success',
                'pdf' => $name_file
            ];

            return response()->json($data, $data['code']);
        }
    }

    public function getPdf($filename)
    {
        $isset = \Storage::disk('pdfs')->exists($filename);
        if ($isset) {
            $file = \Storage::disk('pdfs')->get($filename);
            return new Response($file, 200);
        } else {
            $data = [
                "code" => 404,
                'status' => 'error',
                'message' => 'No existe ese PDF en nuestros servidores.'
            ];
            return response()->json($data, $data['code']);
        }

    }
}