<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Service;
use App\Vehicle;

class ServiceController extends Controller
{
    # Función que nos permite obtener todos los servicios realizados por un usuario
    public function getServicesByUser(Request $request)
    {
        # Obtenemos el id del usuario que nos hace la petición
        $user_id = $request->user_id;

        # Creamos la sentencia SQL que nos permitirá traer todas los servicios realizados por el usuario
        $dataServices = Service::where(
            'operador_id',
            '=',
            $user_id
        )->get();

        # Cargamos la información de sus llaves foraneas
        $dataServices->load('crane');
        $dataServices->load('typeService');
        $dataServices->load('vehicle');

        # Hacemos uso de una función privada el cual nos arma el array solicitado
        $data = $this->createArrayServices($dataServices);

        # Regresamos una respuesta al cliente que solicitó la información
        return response()->json([
            'ok' => true,
            'message' => 'A continuación se muentran los servicios realizados por el usuario',
            'data' => $data
        ], 200);
    }

    /* 
        Función que nos permite obtener los servicios de la semana 
        realizados por un usuario 
    */
    public function getServicesByWeek(Request $request)
    {
        # Hacemos uso de la función privada el cual nos regresa el inicio y fin de semana
        $dateInfo = $this->getWeekInfo();

        # Creamos la sentencia SQL la cual extraerá la inf. ebtre las fechas proporcionadas
        $dataServices = Service::whereBetween('create_date', [
            $dateInfo['start'], $dateInfo['end']
        ])->get();

        # Cargamos la información de sus llaves foraneas
        $dataServices->load('crane');
        $dataServices->load('typeService');
        $dataServices->load('vehicle');

        # Hacemos uso de una función privada el cual nos arma el array solicitado
        $data = $this->createArrayServices($dataServices);

        # Regresamos una respuesta al cliente que solicitó la información
        return response()->json([
            'ok' => true,
            'message' => 'A continuación se muentran los servicios realizados por el usuario',
            'data' => $data
        ], 200);
    }
    /*
        Función que nos permitira actualizar la información que nos proporcione
        el cliente.
    */
    public function update(Request $request){
        # Obtenemos la información que nos proporciona el cliente 
        $json = $request->json;

        # Desglosamos la información obtenida
        # Información para actualizar en el modelo vehicle
        $vehiculo_id = (!is_null($json['vehiculo_id'])) ? Crypt::decryptString($json['vehiculo_id']) : null;
        $placas = (!is_null($json['placas'])) ? $json['placas'] : null;
        $clase = (!is_null($json['clase'])) ? $json['clase'] : null;
        $vehiculo_anio = (!is_null($json['vehiculo_anio'])) ? $json['vehiculo_anio'] : null;
        $color_id = (!is_null($json['color_id'])) ? $json['color_id'] : null;
        $vehiculo_tipo_id = (!is_null($json['vehiculo_tipo_id'])) ? $json['vehiculo_tipo_id'] : null;
        $marca_id = (!is_null($json['marca_id'])) ? $json['marca_id'] : null;
        # Información paara actualizar en el modelo service
        $id = (!is_null($json['id'])) ? Crypt::decryptString($json['id']) : null;
        $destino = (!is_null($json['destino'])) ? $json['destino'] : null;
        $observaciones = (!is_null($json['observaciones'])) ? $json['observaciones'] : null;
        $maniobra_adicional = (!is_null($json['maniobra_adicional'])) ? $json['maniobra_adicional'] : null;
        # id -> 4823, vehiculo_id -> 5152
        
        $service = Service::where('id', $id)->update([
            'selleva' => $destino,
            'observaciones' => $observaciones,
            'maniobraadicional' => $maniobra_adicional,
        ]);

        $vehicle = Vehicle::where('id', $vehiculo_id)->update([
            'placas' => $placas,
            'clase' => $clase,
            'anio' => $vehiculo_anio,
            'colorvehiculo_id' => $color_id,
            'tipovehiculo_id' => $vehiculo_tipo_id,
            'marca_id' => $marca_id,
        ]);

        # Regresamos una respuesta al cliente que solicitó la información
        return response()->json([
            'ok' => true,
            'message' => 'Se ha actualizado la información del servicio',
        ], 200);
    }
    /*
        Función que nos permite extraer el inicio y fin de semana (fechas)
    */
    private function getWeekInfo()
    {
        date_default_timezone_set('America/Cancun');
        $today = date("Y-m-d");
        $startDay = "Monday";
        $endDay = "Sunday";

        $strDate = strtotime("2019-05-18");

        $start = date('Y-m-d', strtotime('last ' . $startDay, $strDate));
        $end = date('Y-m-d', strtotime('next ' . $endDay, $strDate));

        if (date("l", $strDate) == $startDay) {
            $start = date("Y-m-d", $strDate);
        }
        if (date("l", $strDate) == $endDay) {
            $end = date("Y-m-d", $strDate);
        }
        return ["start" => $start . ' 00:00:00', "end" => $end . ' 00:00:00'];
    }
    /*
        Función que nos permite crear un array el cual contiene la información
        de los servicios que nosotros deseemos.
    */
    private function createArrayServices($dataServices){
        # Creamos un array el cual contendra los objetos proporcionados
        $arrayServices = [];

        # Por medio de un foreach extraemos cada uno de los datos desados 
        # que le proporcionamos a la función
        foreach ($dataServices as &$data) {

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
                'vehiculo_id' => Crypt::encryptString($data['vehiculo_id']),
                'vehiculo_anio' => $data['vehicle']['anio'],
                'placas' => $data['vehicle']['placas'],
                'clase' => $data['vehicle']['clase'],
                'color_id' => $data['vehicle']['colorvehiculo_id'],
                'vehiculo_tipo_id' => $data['vehicle']['tipovehiculo_id'],
                'marca_id' => $data['vehicle']['marca_id'],
            ];

            # Por medio de la función "array_push" adjuntamos el data al array antes descrito
            array_push($arrayServices, $newData);
        }

        # Regresamos el array
        return $arrayServices;
    }
}
