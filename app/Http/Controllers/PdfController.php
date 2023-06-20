<?php

namespace App\Http\Controllers;

use App\Models\Pdf;
use App\Models\Propietario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{

    public function __construct()
    {
        $this->middleware('api.auth');
    }
    public function generate($id)
    {
        // consultar los datos del propietario beneficiado que seran añadidos al pdf
        $data = Propietario::select(
            'id',
            'tipo_identificacion_id',
            Propietario::raw("CONCAT(primerNombre, ' ', segundoNombre) AS nombres"),
            Propietario::raw("CONCAT(primerApellido, ' ', segundoApellido) AS apellidos"),
        )
            ->with([
                'beneficio' => function ($query) {
                    $query->select(
                        'id',
                        'propietario_id',
                        'fecha',
                        'fechaEntrega',
                        'tipoBeneficio_id',
                        'descripcion'
                    );
                },
                'beneficio.tipoBeneficio' => function ($query) {
                    $query->select(
                        'id',
                        'categoria_id',
                        'descripcion'
                    );
                }
            ])->find($id);

        //fecha actual del sistema para agregarla al pdf
        $fecha = date('Y-m-d');

        if (empty($data->beneficio) || empty($data)) {
            return response()->json($datos = [
                'code' => 400,
                'status' => 'error',
                'message' => 'no se encontro beneficio asignado a este propietario'
            ], 400);
        } else {
            // Genera el contenido HTML del PDF

            $html = '<!DOCTYPE html>
        <html>
        <head>
        <style>
        body {
            font-family: Arial, sans-serif;
            font-size:18px;
            margin.botton:20px;
            text-aling:justify;
        }
        #header {
            display: grid;
            align-items: center;
            margin-bottom: 20px;
        }
        #header img {
            height: 80px;
            margin-right: 30px;
        }
        #header p {
            font-weight: bold;
            margin: 0;
            font-size:14px;
        }
        #content {
            margin-bottom: 20px;
        }
        #footer {
            text-align: center;
            margin-top: 50px;
        }
        #footer p {
            margin: 0;
        }
        .firma {
            margin-top: 50px;
        }
        table {
            width: 100%;
            max-width: 600px;
            table-layout:fixed;
        }
        </style>
        </head>
        <body>
            <div id="header">
                <table>
                <tr>
                    <th>
                            <img src="' . asset('img/icono.png') . '" alt="Logo">
                    
                    </th>
                    <th>
                        <p>Alcaldía Municipal de Valledupar<br>
                        Oficina de Medio Ambiente<br>
                        Valledupar - Cesar</p>  
                        
                    </th>
                    <th>
                            <p>Código beneficio: <span style="font-weight: bold;">' . $data->beneficio->id . '</span></p>
                            <p>Fecha: <span style="font-weight: bold;">' . $fecha . '</span></p>
                        
                    </th>
                </tr>
                </table>
            </div>
            <br><br>
            <div id="content">
                <p>En esta acta se garantiza la entrega del beneficio titulado
                <span style="font-weight: bold;">' . $data->beneficio->tipoBeneficio->descripcion . '</span> que 
                describe <span style="font-weight: bold;">' . $data->beneficio->descripcion . '</span> a la 
                persona <span style="font-weight: bold;">' . $data->primerNombre . ' ' . $data->primerApellido . '</span> identificado(a) 
                con el número de documento <span style="font-weight: bold;">' . $data->id . '</span>,
                beneficio que fue(o será) entregado en la 
                fecha: <span style="font-weight: bold;">' . $data->beneficio->fechaEntrega . '</span>.</p>
                <br>
                <br>
                <br>
                <p>Esta acta deberá ser firmada por el beneficiario(a) para la 
                confirmación de la entrega del beneficio.</p>
            </div>
            <br><br><br><br><br><br><br><br><br><br><br><br>
            <table>
                <tr>
                    <th>
                    <div id="footer">
                        <div class="firma">
                            <p>____________________________</p>
                            <p>Firma del Beneficiario(a)</p>
                        </div>
                    </div>
                    </th>
                    <th>
                    <div id="footer">
                        <div class="firma">
                            <p>____________________________</p>
                            <p>Firma Representante de la alcaldía</p>
                        </div>
                    </div>
                    </th>
                </tr>
            </table
        </body>
        </html>';

            // Crea una nueva instancia de Dompdf
            $options = new Options();
            $options->set('isRemoteEnabled', true); // Habilita la carga de recursos remotos (si es necesario)
            $dompdf = new Dompdf($options);

            // Renderiza el contenido HTML
            $dompdf->loadHtml($html);

            // Opcional: Personaliza las opciones de configuración de Dompdf
            $dompdf->setPaper('A4', 'portrait');

            // Genera el PDF
            $dompdf->render();

            // Devuelve el PDF como una respuesta
            return response($dompdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="reporte.pdf"');

        }




    }
    
    public function upload(Request $request)
    {
        // Recoger los datos de la petición
        $pdf = $request->file('pdf');
        $id = $request->string('id');

        // Validar el tipo de dato enviado
        $validate = \Validator::make($request->all(), [
            'pdf' => 'required|mimes:pdf'
        ]);

        if (!$pdf || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al intentar subir el archivo, revise que el tipo de archivo sea PDF'
            ];
        } else {
            // Buscar el registro existente por propietario_id
            $pdfExistente = Pdf::where('propietario_id', $id)->first();

            if ($pdfExistente) {
                // Se concatena el tiempo con el nombre del archivo para que no se repitan
                $name_file = time() . $pdf->getClientOriginalName();

                // Eliminar el archivo anterior
                \Storage::disk('pdfs')->delete($pdfExistente->pdf);

                // Actualizar el nombre del archivo en el registro existente
                $pdfExistente->pdf = $name_file;
                $pdfExistente->save();

                // Mover el nuevo archivo al directorio de PDFs
                \Storage::disk('pdfs')->put($name_file, \File::get($pdf));
            } else {
                // Se concatena el tiempo con el nombre del archivo para que no se repitan
                $name_file = time() . $pdf->getClientOriginalName();

                // Crear una nueva instancia del modelo Pdf
                $nuevoPdf = new Pdf();
                $nuevoPdf->propietario_id = $id;
                $nuevoPdf->pdf = $name_file;
                $nuevoPdf->save();

                // Almacenar el nuevo archivo en el directorio de PDFs
                \Storage::disk('pdfs')->put($name_file, \File::get($pdf));
            }

            $data = [
                'code' => 200,
                'status' => 'success',
                'pdf' => $name_file
            ];

            return response()->json($data, $data['code']);
        }
    }

    public function destroy($id)
    {
        // Buscar el registro existente por propietario_id
        $pdfExistente = Pdf::where('propietario_id', $id)->first();
        if ($pdfExistente) {
            try {
                // Elimina el pdf y su ruta desde storage/app/public/pdfs
                \Storage::disk('pdfs')->delete($pdfExistente->pdf);
            } catch (\Exception $exception) {
                dd($exception->getMessage());
            } finally {
                $pdfExistente->delete();
            }
            $data = [
            'code'=>200,
            'status'=>'success',
            'message'=>'PDF eliminado correctamente'];

            return response()->json($data,$data['code']);
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