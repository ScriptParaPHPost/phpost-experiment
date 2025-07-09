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

require_once __DIR__ . '/../class/c.email.php';

class tsVerificar {

	protected tsCore $tsCore;

	protected Junk $Junk;

	protected tsEmail $Email;

	protected PasswordManager $PasswordManager;

	public $uid;

	private int $limitLife = 600; // 10 Min

	public function __construct(Verificar $deps, Config $config) {
		$this->tsCore = $deps->tsCore;
		$this->Junk = $deps->Junk;
		$this->PasswordManager = $deps->PasswordManager;
		$this->Email = new tsEmail($config->get('mail'));
	}

	public function verifyEmail(string $email = '', string $action = '') {
		$user_info = db_exec([__FILE__, __LINE__], 'query', "SELECT user_id, user_name, user_registro, user_activo FROM u_miembros WHERE user_email = '$email'");
		if(empty($user_info)) return 'No hay solicitudes o ya fueron realizadas.';
		if(!db_exec('num_rows', $user_info)) {
			return 'El email no se encuentra registrado.';
		}
		$user_info = db_exec('fetch_assoc', $user_info);
		if((int)$user_info['user_activo'] === 1 && $action !== 'verificar-password') {
			return 'La cuenta ya se encuentra activada.';
		}
		return $user_info;
	}

	public function verifyToDB(int $uid = 0, string $email = '', int $type = 0, int $key = 0) {
		return (db_exec([__FILE__, __LINE__], 'query', "INSERT INTO w_contacts (user_id, user_email, `time`, `type`, `hash`) VALUES ($uid, '$email', {$this->Junk->setTime()}, $type, '$key')"));
	}

	public function sendEmail(array $tsData = [], array $data = []) {
		$to = $tsData['user_email'];
		$this->Email->asunto = $data['asunto'];
		$this->Email->plantilla = $data['plantilla'];
		if(!$this->Email->enviar($to,
		   [
		   	'usuario' => $tsData['user_name'],
		      'sitio' => $this->tsCore->settings['titulo'],
		      'enlace' => "{$this->tsCore->settings['url']}/{$data['page']}/{$data['key']}/{$data['type']}/$to",
		      'protocolo' => "{$this->tsCore->settings['url']}/pages/protocolo/"
		   ]
		)) return 'Hubo un error al intentar procesar lo solicitado';
		return $data['mensaje'];
	}

	public function delContactsOld($tsData) {
		if(!is_array($tsData)) return true;
		$time = $this->Junk->setTime() - $this->limitLife; // 10 Minutos
		#db_exec([__FILE__, __LINE__], 'query', "DELETE FROM `w_contacts` WHERE `time` < $time");
		// EXISTE?
		return true;#count($tsData) < 4;
	}

	public function verifyCode(string $email = '', int $code = 0, int $type = 0) {
		$code = db_exec([__FILE__, __LINE__], 'query', "SELECT id, user_id, user_email, time, type, hash FROM w_contacts WHERE hash = '$code' AND user_email = '$email' AND type = $type ORDER BY id DESC LIMIT 1");
		if(empty($code)) return true;
		return !db_exec('num_rows', $code);
	}

	private function createArray(string $title = '', string $message = '', string $btnTxt = '', string $btnLink = '') {
		return [
			'titulo' => $title, 
			'mensaje' => $message, 
			'botonTexto' => $btnTxt, 
			'botonLink' => "{$this->tsCore->settings['url']}/$btnLink"
		];
	}

	private function delRecord(int $uid = 0) {
		db_exec([__FILE__, __LINE__], 'query', "DELETE FROM w_contacts WHERE user_id = $uid");
	}

	public function changePassword(int $uid = 0, string $newPassword = '', int $code = 0, string $email = '') {
		if($newPassword === '') {
			return $this->createArray('Opps!', 'Escriba una contrase&ntilde;a', 'Volver a reintentar', "password/{$code}/1/{$email}");
		}
		$newPassword = $this->PasswordManager->hash($newPassword);
		if(db_exec([__FILE__, __LINE__], 'query', "UPDATE u_miembros SET user_password = '$newPassword' WHERE user_id = $uid")) {
			$this->delRecord($uid);
			return $this->createArray('Bien!', 'La contrase&ntilde;a ha sido actualizada', 'Ir a la p&aacute;gina principal');
		}
	}

	public function accountActive(int $uid = 0, int $code = 0, string $email = '') {
		if(!db_exec([__FILE__, __LINE__], 'query', "UPDATE u_miembros SET user_activo = 1 WHERE user_id = $uid")) {
			return $this->createArray('Opps!', 'Ha ocurrido un error', 'Reintentar', "validar/{$code}/1/{$email}");
		}
		$this->delRecord($uid);
		return $this->createArray('Bien!', 'Cuenta validada y activada', 'Ir a la p&aacute;gina principal');
	}

}