<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'cms_padsolicitudes';

    public $timestamps = false;

    public function typeService(){
    	// Regresa todos los datos del modelo User donde el usuario sea igual al 'user_id' proporcionado
    	return $this->belongsTo('App\TypeService', 'tiposervicio_id');
    }

    public function crane(){
    	// Regresa todos los datos del modelo User donde el usuario sea igual al 'user_id' proporcionado
    	return $this->belongsTo('App\Crane', 'grua_id');
    }

    public function user(){
    	// Regresa todos los datos del modelo User donde el usuario sea igual al 'user_id' proporcionado
    	return $this->belongsTo('App\User', 'operador_id');
    }

    public function currency(){
    	// Regresa todos los datos del modelo User donde el usuario sea igual al 'user_id' proporcionado
    	return $this->belongsTo('App\Currency', 'currency_id');
    }

    public function vehicle(){
    	// Regresa todos los datos del modelo User donde el usuario sea igual al 'user_id' proporcionado
    	return $this->belongsTo('App\Vehicle', 'vehiculo_id');
    }

}
