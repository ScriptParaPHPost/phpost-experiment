<?php

declare(strict_types=1);

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
 * Aplicando Single Responsibility
*/

if (!defined('PHPOST_CORE_LOADED')) {
	exit('Acceso denegado: ¡No puedes acceder este script directamente!');
}

require_once __DIR__ . '/../class/c.email.php';

class tsRegistro {

	protected tsCore $tsCore;

	protected tsAuthentication $tsAuthentication;

	protected Junk $Junk;

	protected tsEmail $Email;

	protected PasswordManager $PasswordManager;

	protected Avatar $Avatar;

	public function __construct(Registro $deps) {
		$this->tsCore = $deps->tsCore;
		$this->tsAuthentication = $deps->tsAuthentication;
		$this->Junk = $deps->Junk;
		$this->PasswordManager = $deps->PasswordManager;
		$this->Avatar = $deps->Avatar;
		$this->Email = new tsEmail($deps->Config->get('mail'));
	}

	/**
	 * @name getDataUser($field, false)
	 * @access private
	 * @param string
	 * @return string|array
	*/
	private function getDataUser(string $field = ''): array|string {
		$data['nickname'] = $this->tsCore->setSecure(filter_input(INPUT_POST, 'nickname', FILTER_UNSAFE_RAW) ?? '');
		$data['password'] = $this->tsCore->setSecure(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW) ?? '');
		$data['email'] = $this->tsCore->setSecure(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?? '');
		$data['sexo'] = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_NUMBER_INT) ?? 3;
		$data['terminos'] = filter_input(INPUT_POST, 'terminos', FILTER_VALIDATE_BOOLEAN) ?? false;
		$data['response'] = $this->tsCore->setSecure(filter_input(INPUT_POST, 'response', FILTER_UNSAFE_RAW) ?? '');
		return empty($field) ? $data : $data[$field];
	}

	/**
	 * @name extractEmailDomain($email, false)
	 * @access private
	 * @param string
	 * @return string
	*/
	private function extractEmailDomain(string $email = '', bool $before = true): string  {
		return empty($email) ? '' : $this->tsCore->setSecure(strstr($email, '@', $before));
	}

	/**
	 * @name emailBlacklist($nickname, $email)
	 * @access private
	 * @param string
	 * @param string
	 * @return bool
	*/
	private function emailBlacklist(string $nickname = '', string $email = ''): bool {
		return (db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', "SELECT id FROM w_blacklist WHERE (type = 3 AND value = '{$this->extractEmailDomain($email)}') OR (type = 4 AND value = '{$this->extractEmailDomain($email, true)}') OR (type = 4 AND value = '$nickname') LIMIT 1")) > 0);
	}

	/**
	 * @name emailExists($nickname, $email)
	 * @access private
	 * @param string
	 * @return bool
	*/
	private function emailExists(string $nickname = '', string $email = ''): bool {
		return (db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', "SELECT `user_id` FROM `u_miembros` WHERE user_name = '{$nickname}' OR user_email = '{$email}' LIMIT 1")) > 0);
	}
  
	/**
	 * @name checkUserEmail()
	 * @access public
	 * @return array
	*/
	public function checkUserEmail(): array {
		// Variables
		$data = $this->getDataUser();
		$nickname = $data['nickname'];
		$email = $data['email'];
		$which = empty($nickname) ? 'email' : 'nick';
		if ($nickname !== '' && (ctype_digit($nickname) || !preg_match('/^[a-zA-Z0-9_.-]{3,16}$/', $nickname))) {
			return ['success' => false, 'message' => 'El nick no tiene un formato permitido.'];
		}

		if(!empty($nickname) || !empty($email)) {
			if($this->emailExists($nickname, $email)) {
				return ['success' => false, 'message' => "El $which ya se encuentra registrado."];
			}
			if ($this->emailBlacklist($nickname, $email)) {
				return ['success' => false, 'message' => "Parte del $which no esta permitida"];
			}
			return ['success' => true, 'message' => "El $which esta disponible."];
		} else return ['success' => false, 'message' => 'Faltan datos y no se puede procesar tu solicitud.'];
	}

	private function verifyUserEmailExists(array $tsData = []): bool {
		// COMPROBAR NUEVAMENTE QUE EL USUARIO O EMAIL NO SE ENCUENTREN REGISTRADOS
		$filtrar = filter_var($tsData['user_email'], FILTER_VALIDATE_EMAIL);
		$query = db_exec([__FILE__, __LINE__], 'query', "SELECT `user_name`,`user_email` FROM `u_miembros` WHERE user_name = '{$tsData['user_nickname']}' OR user_email = '{$tsData['user_email']}' LIMIT 1");
		return (db_exec('num_rows', $query) > 0 || !$filtrar || (int)$this->tsCore->settings['c_reg_active'] === 0);
	}

	private function insertUserToDB(array $tsData = []) {
		$createKey = $this->PasswordManager->hash($tsData['user_password']);
		$rango = (int)$this->tsCore->settings['c_reg_rango'] ?? 3;
		return db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `u_miembros` (`user_name`, `user_password`, `user_email`, `user_rango`, `user_registro`) VALUES ('{$tsData['user_nickname']}', '$createKey', '{$tsData['user_email']}', $rango, {$tsData['user_registro']})");
	}

	private function welcome(array $tsData = []) {
		$uid = (int)$tsData['user_id'];
		$query = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT c_met_welcome, c_message_welcome FROM w_config_users WHERE tscript_id = 1"));
		// MENSAJE PARA DAR LA BIENVENIDA BIENVENIDA
		$send_welcome = (int)$query['c_met_welcome'];
		if($send_welcome > 0 AND $send_welcome < 4) {
			$welcome = 'Bienvenid' . ((int)$tsData['user_sexo'] === 1 ? 'o' : 'a'); 
			$msg_bienvenida = str_replace(
				['{{usuario}}', '{{bienvenida}}', '{{sitio}}'], 
				[$tsData['user_nickname'], $welcome, $this->tsCore->settings['titulo']], 
				$this->tsCore->parseBBCode($query['c_message_welcome'])
			);
			$subject = "$welcome a {$this->tsCore->settings['titulo']}";
			switch($send_welcome) {
				case 1:
					db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `u_muro` (p_user, p_user_pub, p_date, p_body, p_type) VALUES ($uid, 1, {$tsData['user_registro']}, '$msg_bienvenida', 1)"); 
					$m_id = (int)db_exec('insert_id');
					db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `u_monitor` (user_id,obj_user,obj_uno, not_type,not_total,not_menubar,not_monitor) VALUES ($uid, 1, $m_id, 12, 1, 1, 1)");
				break;
				case 2:
					$preview = substr($msg_bienvenida, 0, 72) . '...'; 
					if(db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `u_mensajes` (`mp_to`, `mp_from`, `mp_subject`, `mp_preview`, `mp_date`) VALUES ($uid, 1, '$subject', '$preview', {$tsData['user_registro']})")) {
						$mp_id = (int)db_exec('insert_id');
						db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `u_respuestas` (mp_id, mr_from, mr_body, mr_ip, mr_date) VALUES ($mp_id, 1, '$msg_bienvenida', '{$this->Junk->getValue('IP')}', {$tsData['user_registro']})"); 
					}
				break;
			 	case 3:
					db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `u_avisos` (`user_id`, `av_subject`, `av_body`, `av_date`, `av_type`) VALUES ($uid, '$subject', '$msg_bienvenida', {$tsData['user_registro']}, 4)");			
				break;
			}
		}
	}

	private function insertOtherData(array $tsData = []) {
		db_exec([__FILE__, __LINE__], "query", "INSERT INTO u_perfil (user_id, user_sexo) VALUES({$tsData['user_id']}, {$tsData['user_sexo']})");
		db_exec([__FILE__, __LINE__], "query", "INSERT INTO u_portal (user_id) VALUES({$tsData['user_id']})");
		// Creamos el avatar aleatoriamente
		$this->Avatar->get($tsData['user_id'], $tsData['user_nickname']);
		// Damos mensaje de bienvenida
		$this->welcome($tsData);
	}

	private function sendEmail(array $tsData = []) {
		$now = $this->Junk->setTime();
		$to = $tsData['user_email'];
		// Si la cuenta debe ser activada desde el correo
		if((int)$this->tsCore->settings['c_reg_activate'] === 0) {
			$key = substr(md5(time()), 0, 32);
			if(db_exec([__FILE__, __LINE__], 'query', "INSERT INTO w_contacts (user_id, user_email, time, type, hash) VALUES ((int){$tsData['user_id']}, '{$tsData['user_email']}', $now, 2, '$key')")) {
				// DAMOS BIENVENIDA POR CORREO
				$this->Email->asunto = 'Active su cuenta';
				$this->Email->plantilla = 'activar_cuenta';
				if(!$this->Email->enviar($to,
				   [
				      'usuario' => $tsData['user_nickname'],
				      'password' => $tsData['user_password'],
				      'sitio' => $this->tsCore->settings['titulo'],
				      'enlace' => "{$this->tsCore->settings['url']}/validar/$key/2/{$tsData['user_email']}",
				      'protocolo' => "{$this->tsCore->settings['url']}/pages/protocolo/"
				   ]
				)) return ['success' => false, 'message' => 'Hubo un error al intentar procesar lo solicitado'];
				return ['success' => true, 'message' => "Te hemos enviado un correo a <b>$to</b> con los ultimos pasos para finalizar con el registro.\n\nSi en los proximos minutos no lo encuentras en tu bandeja de entrada, por favor, revisa tu carpeta de correo no deseado, es posible que se haya filtrado.\n\nMuchas gracias"];	
			}
		} else {
			$this->Email->asunto = "Bienvenido a {$this->tsCore->settings['titulo']}";
			$this->Email->plantilla = 'bienvenida_usuario';
			$this->Email->enviar($to,
			   [
			      'usuario' => $tsData['user_nickname'],
			      'password' => $tsData['user_password'],
			      'sitio' => $this->tsCore->settings['titulo'],
			      'enlace' => $this->tsCore->settings['url']
			   ]
			);
			$this->tsAuthentication->userActivate($tsData['user_id'], md5((string)$tsData['user_registro']));
			$this->tsAuthentication->loginUser(true, $tsData['user_nickname'], $tsData['user_password']);
			return ['success' => true, 'message' => "Bienvenido a <b>{$this->tsCore->settings['titulo']}</b>, Ahora estas registrado y tu cuenta ha sido activada, podras disfrutar de esta comunidad inmediatamente.\n\nMuchas gracias"];
		}
	}

	private function checkedRecaptcha(string $response = '') {
		require_once __DIR__ . '/../utils/reCaptcha.php';
		/**
		 * Comprobamos el recaptcha v3
		*/
		$reCaptcha = new reCaptchaV3();
		$reCaptcha->RemoteIP = $this->Junk->getValue('IP');
		$reCaptcha->SecretKey = $this->Junk->setKeys('skey');
		$response = $reCaptcha->verify($response);
		if (!$response) return ['success' => false, 'message' => 'reCaptcha: No hemos podido validar tu humanidad'];
	}

	/**
	 * @name newRegister()
	 * @access public
	 * @param none
	 * @return string
	*/
	public function newUserRegister() {
		$getData = $this->getDataUser();
		$data['user_registro'] = $this->Junk->setTime();
		foreach($getData as $key => $value) {
			$data["user_$key"] = is_numeric($value) ? (int)$value : (is_string($value) ? (string)$value : (bool)$value);
		}
		$this->checkedRecaptcha($data['user_response']);
		// COMPROBAR QUE EL NOMBRE DE USUARIO SEA VÁLIDO
		if(!preg_match("/^[a-zA-Z0-9_-]{4,16}$/", $data['user_nickname']) ) {
			return ['success' => false, 'message' => 'nickname: Nombre de usuario invalido'];
		}
		if($this->verifyUserEmailExists($data)) {
			return ['success' => false, 'message' => 'Hubo problemas al intentar registrarle, hay campos vac&iacute;os, inv&aacute;lidos o no se le permite el registro.'];
		}
		if($this->insertUserToDB($data)) {
			// Obtenemos el ID
			$data['user_id'] = (int)db_exec('insert_id');
			// Insertamos la informacion necesaria
			$this->insertOtherData($data);
			// Enviamos correo de activación o de bienvenida al usuario!
			return $this->sendEmail($data);
		}
	}
}