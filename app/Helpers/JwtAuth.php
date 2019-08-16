<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Crypt;

class JwtAuth
{
	public $key;

	public function __construct()
	{
		$this->key = env('KEY_JWT');
	}

	public function createJwt($user_id)
	{
		$today = date('Y-m-d 00:00:00');
		$today_unix = strtotime($today);
		$tomorrow = strtotime($today . "+ 1 days");

		$token = [
			'sub' => Crypt::encryptString($user_id),
			'iat' => $today_unix,
			'exp' => $tomorrow
		];

		return $jwt = JWT::encode($token, $this->key, 'HS256');
	}

	public function checkToken($jwt)
	{
		try {
			$decoded = JWT::decode($jwt, $this->key, array('HS256'));
			$data = json_decode(json_encode($decoded),true);
		} catch (\UnexpectedValueException $e) {
			return [
				'ok' => 'false',
				'message' => 'El valor del token no se puede decodificar',
				'code' => 400
			];
		} catch (\DomainException $e) {
			return [
				'ok' => 'false',
				'message' => 'el dominio del token no es valido',
				'code' => 400
			];
		}

		$now = strtotime("now");
		$exp = $data['exp'];

		if($now > $exp){
			return [
				'ok' => 'false',
				'message' => 'El token ha caducado',
				'code' => 202
			];
		}else{
			return [
				'ok' => 'true',
				'message' => 'El token aún no ha caducado',
				'user_id' => Crypt::decryptString($data['sub']),
				'code' => 200
			];
		}
	}
}
