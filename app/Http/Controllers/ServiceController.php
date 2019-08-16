<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\JwtAuth;
use App\Service;

class ServiceController extends Controller
{
    public function getServicesByUser(Request $request)
    {
        $user_id = $request->user_id;
        $dataServices = Service::where(
            'operador_id',
            '=',
            $user_id
        )->get();
        $dataServices->load('crane');
        $dataServices->load('typeService');
        $dataServices->load('vehicle');

        $arrayServices = [];

        foreach($dataServices as &$data){

            $newData = [
                'id' => Crypt::encryptString($data['id']),
                'referencia' => $data['referencias'],
                'tipo_servicio' => $data['typeService']['name'],
                'fecha' => $data['fecha'],
                'modena' => $data['currency']['name'],
                'destino' => $data['selleva'],
                'solicitante' => $data['solicito'],
                'expediente' => $data['noexpediente'],
                'asegurado' => $data['asegurado'],
                'arribo' => $data['seencuentra'],
                'nombre_solicitante' => $data['solicito'],
                'observaciones' => $data['observaciones'],
                'maniobra_adicional' => $data['maniobraadicional'],
                'hora_inicio' => $data['horainicio'],
                'hora_final' => $data['fechahorarealarribo'],
                'costo' => $data['amount_total'],
                'vehiculo_anio' => $data['vehicle']['anio'],
                'no_serie' => $data['vehicle']['name'],
                'placas' => $data['vehicle']['placas'],
                'clase' => $data['vehicle']['clase'],
                'vehiculo_anio' => $data['vehicle']['anio'],

            ];

            array_push($arrayServices, $newData);
        }

        return response()->json([
            'ok' => true,
            'data' => $arrayServices,
            'A continuación se muentran los servicios realizados por el usuario'
        ], 200);
    }
}
