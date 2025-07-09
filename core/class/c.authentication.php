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

class tsAuthentication {

	protected tsCore $tsCore;

	protected tsUser $tsUser;

	protected tsSession $session;

	protected Junk $Junk;

	protected PasswordManager $PasswordManager;

	public $uid;

	public function __construct(Authentication $deps) {
		$this->tsCore = $deps->tsCore;
		$this->tsUser = $deps->tsUser;
	  	$this->session = $deps->tsSession;
		$this->Junk = $deps->Junk;
		$this->PasswordManager = $deps->PasswordManager;
	  	$this->setSession();
	}

	public function setSession() {
	  // Si no existe una sessión la creamos
	  // si existe la actualizamos...
		if (!$this->session->read()) $this->session->create();
		else {
			$this->session->update();
			$this->tsUser->loadUser();
		}
	}

	/*
		userActivate()
	*/
	public function userActivate(int $tsUserID = 0, string $tsKey = ''){
		if(empty($tsUserID)) $tsUserID = (int)$_GET['uid'];
		if(empty($tsKey)) $tsKey = $this->tsCore->setSecure($_GET['key']);
		//
		$query = db_exec([__FILE__, __LINE__], 'query', "SELECT user_name, user_password, user_registro FROM u_miembros WHERE user_id = '$tsUserID' LIMIT 1");
		$tsData = db_exec('fetch_assoc', $query);	// CARGAMOS DATOS
		//
		if(db_exec('num_rows', $query) === 0 || $tsKey !== md5($tsData['user_registro'])) return false;
		
		if(!db_exec([__FILE__, __LINE__], 'query', "UPDATE u_miembros SET user_activo = 1 WHERE user_id = '$tsUserID'")) return false;
		return $tsData;
	}

	private function getDataUser(): array {
		$data['nickname'] = $this->tsCore->setSecure(filter_input(INPUT_POST, 'nickname', FILTER_UNSAFE_RAW) ?? '');
		$data['password'] = $this->tsCore->setSecure(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW) ?? '');
		$data['remember'] = filter_input(INPUT_POST, 'remember', FILTER_VALIDATE_BOOLEAN) ?? false;
		return $data;
	}

	private function loadDataUser(int $uid = 0, bool $remember = false) {
		$this->uid = $uid;
		// Actualizamos la session
		$this->session->update($uid, $remember, TRUE);
		// Cargamos la información del usuario
		$this->tsUser->loadUser(true);
		// COMPROBAMOS SI TENEMOS QUE ASIGNAR MEDALLAS
		#$this->DarMedalla();
	}

	/*
		HACEMOS LOGIN
		loginUser($username, $password, $remember = false, $redirectTo = NULL);
	*/
	public function loginUser(string|bool $redirectTo, string $nickname = '', string $password = ''){
		$data = $this->getDataUser();
		if($redirectTo || !empty($redirectTo)) {
			$data['nickname'] = $nickname;
			$data['password'] = $password;
		}
		
		if(empty($data['nickname']) || empty($data['password'])) {
			return json_encode(['status' => false, 'message' => 'Faltan datos para completar...']);
		}
		# Consultamos
		$user = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT user_id, user_name, user_password, user_activo, user_baneado FROM u_miembros WHERE user_name = '{$data['nickname']}' OR user_email = '{$data['nickname']}' LIMIT 1"));

		# Comprobamos que el usuario exista
		if(empty($user)) {
			return json_encode(['status' => false, 'message' => 'El usuario no existe.']);
		} 
		# Comprobamos la contraseña
		if(!$this->PasswordManager->verify($data['password'], $user['user_password'])) {
			return json_encode(['status' => false, 'message' => 'Tu contrase&ntilde;a es incorrecta.']);
		} 
		# Comprobamos que el usuario este activo
		if((int)$user['user_activo'] === 0) {
			return json_encode(['status' => false, 'message' => 'Debes activar tu cuenta.']);
		} 
		$this->loadDataUser((int)$user['user_id'], $data['remember']);
		# Éxito
		$redirectTo = $this->tsCore->settings['url'] . (is_string($redirectTo) ? $redirectTo : '/');
		return json_encode(['status' => true, 'message' => 'Inicio de sesión exitoso', 'redirect' => $redirectTo]);
	}

	/*
		CERRAR SESSION
		logoutUser($redirectTo)
	*/
	public function logoutUser($user_id, string $redirectTo = '/') {
		$this->session->read();
		$this->session->destroy();
		/* LIMPIAR VARIABLES */
		$this->info = [];
		$this->is_member = 0;
		# UPDATE
		$last_active = (time() - (((int)$this->tsCore->settings['c_last_active'] * 60) * 3));
		db_exec([__FILE__, __LINE__], 'query', "UPDATE u_miembros SET user_lastactive = $last_active WHERE user_id = $user_id");
		/* REDERIGIR */
		if($redirectTo != NULL) $this->tsCore->redirectTo($redirectTo);	// REDIRIGIR
		else return true;
	}

	/*
		DarMedalla()
	*/
	function DarMedalla(){
		//
	  $q1 = db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', 'SELECT wm.medal_id FROM w_medallas AS wm LEFT JOIN w_medallas_assign AS wma ON wm.medal_id = wma.medal_id WHERE wm.m_type = \'1\' AND wma.medal_for = \''.$this->uid.'\''));        
		$q2 = db_exec('fetch_row', db_exec([__FILE__, __LINE__], 'query', 'SELECT COUNT(follow_id) AS f FROM u_follows WHERE f_id = \''.$this->uid.'\' && f_type = \'1\''));
		$q3 = db_exec('fetch_row', db_exec([__FILE__, __LINE__], 'query', 'SELECT COUNT(follow_id) AS s FROM u_follows WHERE f_user = \''.$this->uid.'\' && f_type = \'1\''));
		$q4 = db_exec('fetch_row', db_exec([__FILE__, __LINE__], 'query', 'SELECT COUNT(cid) AS c FROM p_comentarios WHERE c_user = \''.$this->uid.'\' && c_status = \'0\''));
		$q5 = db_exec('fetch_row', db_exec([__FILE__, __LINE__], 'query', 'SELECT COUNT(cid) AS cf FROM f_comentarios WHERE c_user = \''.$this->uid.'\''));
		$q6 = db_exec('fetch_row', db_exec([__FILE__, __LINE__], 'query', 'SELECT COUNT(foto_id) AS fo FROM f_fotos WHERE f_status = \'0\' && f_user = \''.$this->uid.'\''));
		$q7 = db_exec('fetch_row', db_exec([__FILE__, __LINE__], 'query', 'SELECT COUNT(post_id) AS p FROM p_posts WHERE post_user = \''.$this->uid.'\' && post_status = \'0\''));
		// MEDALLAS
		$datamedal = result_array($query = db_exec([__FILE__, __LINE__], 'query', 'SELECT medal_id, m_cant, m_cond_user, m_cond_user_rango FROM w_medallas WHERE m_type = \'1\' ORDER BY m_cant DESC'));
		//		
		foreach($datamedal as $medalla){
			// DarMedalla
			if($medalla['m_cond_user'] == 1 && !empty($this->info['user_puntos']) && $medalla['m_cant'] > 0 && $medalla['m_cant'] <= $this->info['user_puntos']){
				$newmedalla = $medalla['medal_id'];
			}elseif($medalla['m_cond_user'] == 2 && !empty($q2[0]) && $medalla['m_cant'] > 0 && $medalla['m_cant'] <= $q2[0]){
				$newmedalla = $medalla['medal_id'];
			}elseif($medalla['m_cond_user'] == 3 && !empty($q3[0]) && $medalla['m_cant'] > 0 && $medalla['m_cant'] <= $q3[0]){
				$newmedalla = $medalla['medal_id'];
			}elseif($medalla['m_cond_user'] == 4 && !empty($q4[0]) && $medalla['m_cant'] > 0 && $medalla['m_cant'] <= $q4[0]){
				$newmedalla = $medalla['medal_id'];
			}elseif($medalla['m_cond_user'] == 5 && !empty($q5[0]) && $medalla['m_cant'] > 0 && $medalla['m_cant'] <= $q5[0]){
				$newmedalla = $medalla['medal_id'];
			}elseif($medalla['m_cond_user'] == 6 && !empty($q7[0]) && $medalla['m_cant'] > 0 && $medalla['m_cant'] <= $q7[0]){
				$newmedalla = $medalla['medal_id'];
			}elseif($medalla['m_cond_user'] == 7 && !empty($q6[0]) && $medalla['m_cant'] > 0 && $medalla['m_cant'] <= $q6[0]){
				$newmedalla = $medalla['medal_id'];
			}elseif($medalla['m_cond_user'] == 8 && !empty($q1) && $medalla['m_cant'] > 0 && $medalla['m_cant'] <= $q1){
				$newmedalla = $medalla['medal_id'];
			}elseif($medalla['m_cond_user'] == 9 && !empty($this->info['user_rango']) && $medalla['m_cant'] > 0 && $medalla['m_cond_user_rango'] == $this->info['user_rango']){
				$newmedalla = $medalla['medal_id'];
			}
			//SI HAY NUEVA MEDALLA, HACEMOS LAS CONSULTAS
			if(!empty($newmedalla)){
				if(!db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', 'SELECT id FROM w_medallas_assign WHERE medal_id = \''.(int)$newmedalla.'\' && medal_for = \''.$this->uid.'\''))) {
					db_exec([__FILE__, __LINE__], 'query', 'INSERT INTO `w_medallas_assign` (`medal_id`, `medal_for`, `medal_date`, `medal_ip`) VALUES (\''.(int)$newmedalla.'\', \''.$this->uid.'\', \''.time().'\', \''.$_SERVER['REMOTE_ADDR'].'\')');
					db_exec([__FILE__, __LINE__], 'query', 'INSERT INTO u_monitor (user_id, obj_uno, not_type, not_date) VALUES (\''.$this->uid.'\', \''.(int)$newmedalla.'\', \'15\', \''.time().'\')');
					db_exec([__FILE__, __LINE__], 'query', 'UPDATE w_medallas SET m_total = m_total + 1 WHERE medal_id = \''.(int)$newmedalla.'\'');
				}
			}
	   }
	}

}