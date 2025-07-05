<?php

declare(strict_types=1);

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

if (!defined('PHPOST_CORE_LOADED')) {
	exit('Acceso denegado: ¡No puedes acceder este script directamente!');
}

class reCaptchaV3 {

	public string $RemoteIP;

	public string $SecretKey;

	public function __construct() {
	}

	/**
	 * Verifica el token de reCaptcha v3.
	 *
	 * @param string $response Token enviado por el frontend
	 * @return bool true si es válido, false si no
	 */
	public function verify(string $response): bool {
		if (empty($response)) return false;

		$api = 'https://www.google.com/recaptcha/api/siteverify';

		$postFields = [
			'secret'   => $this->SecretKey,
			'response' => $response,
			'remoteip' => $this->RemoteIP
		];

		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL            => $api,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => http_build_query($postFields),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 10,
			CURLOPT_SSL_VERIFYPEER => true, // Más seguro
		]);

		$result = curl_exec($curl);

		if ($result === false || curl_errno($curl)) {
			curl_close($curl);
			return false;
		}

		curl_close($curl);
		$data = json_decode($result, true);

		if (!is_array($data) || !isset($data['success'])) {
			return false;
		}

		// Puedes validar el hostname también (opcional y más estricto)
		/*
		if ($data['hostname'] !== $_SERVER['SERVER_NAME']) {
			return false;
		}
		*/

		return $data['success'] === true;
	}
}
