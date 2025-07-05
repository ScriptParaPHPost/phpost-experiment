<?php

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

if (!defined('PHPOST_CORE_LOADED')) 
	exit('Acceso denegado: ¡No puedes acceder este script directamente!');

class tsUser {

	protected tsCore $tsCore;

	protected tsSession $session;

	protected Junk $Junk;

	protected Avatar $Avatar;

	public $info; // SI EL USUARIO ES MIEMBRO CARGAMOS DATOS DE LA TABLA

	public int $is_member = 0; // EL USUARIO ESTA LOGUEADO?

	public int $is_admod = 0;

	public int $is_banned = 0;

	public string $nick = 'Visitante'; // NOMBRE A MOSTRAR

	public int $uid = 0; // USER ID
	
	public $permisos;

	public string $avatar;

	public function __construct(User $deps) {
		/* CARGAR SESSION */
		$this->tsCore = $deps->tsCore;
		$this->Junk = $deps->Junk;
		$this->Avatar = $deps->Avatar;
	  	$this->session = $deps->tsSession;
	  	$this->setSession();
		# Esta logueado, actualiza puntos por día
		if($this->is_member) $this->puntos_actualizados();
	}

	public function getCore(): tsCore {
      return $this->tsCore;
   }

	public function getJunk(): Junk {
      return $this->Junk;
   }

	public function getAvatar(): Avatar {
      return $this->Avatar;
   }

	/**
	 * Función para la actualización de puntos
	*/
	public function puntos_actualizados() {
		// HORA EN LA CUAL RECARGAR PUNTOS 0 = MEDIA NOCHE DEL SERVIDOR
		$ultimaRecarga = (int)$this->info['user_nextpuntos'];
		$tiempoActual = time();
		// SI YA SE PASO EL TIEMPO RECARGAMOS...
		if ($ultimaRecarga < $tiempoActual) {
		   // CALCULAR LA SIGUIENTE RECARGA A LAS 24 HRS
		   $sigRecarga = strtotime('tomorrow', $tiempoActual);
		   // ACTUALIZAR LA BASE DE DATOS
		   $keep_points = (int)db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT c_keep_points FROM w_config_users WHERE tscript_id = 1"))['c_keep_points'];
		   $puntosxdar = $keep_points === 0 ? $this->permisos['gopfd'] : 'user_puntosxdar + '.$this->permisos['gopfd'];
		   db_exec([__FILE__, __LINE__], 'query', "UPDATE u_miembros SET user_puntosxdar = $puntosxdar, user_nextpuntos = $sigRecarga WHERE user_id = {$this->uid}");
		   // VAMONOS
		   return true;
		}
	}

	/*
		CARGA LA SESSION
		setSession()
	*/
	public function setSession() {
	  // si existe la actualizamos...
		if (!$this->session->read()) $this->session->create();
		else {
		 	// Actualizamos sesión
			$this->session->update();
		 	// Cargamos información
		 	$this->loadUser();
		}
	}

	private function loadUserRango() {
		// PERMISOS SEGUN RANGO
	  	$this->info['rango'] = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT r_name, r_color, r_image, r_allows FROM u_rangos WHERE rango_id = {$this->info['user_id']} LIMIT 1"));
		// PERMISOS SEGUN RANGO
		$datis = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT r_allows FROM u_rangos WHERE rango_id = {$this->info['user_rango']} LIMIT 1"));
		$this->permisos = unserialize($datis['r_allows']);
		if(!isset($this->permisos['moat'])) $this->permisos['moat'] = false;
		if(!isset($this->permisos['sumo'])) $this->permisos['sumo'] = false;
		if(!isset($this->permisos['suad'])) $this->permisos['suad'] = false;
		/* ES MIEMBRO */
		$this->is_member = 1;
			
		$this->is_admod = match (true) {
		   !$this->permisos['sumo'] && $this->permisos['suad'] => 1,
		   $this->permisos['sumo'] && !$this->permisos['suad'] => 2,
		   $this->permisos['sumo'] || $this->permisos['suad']  => true,
		   default => 0,
		};
	}

	/*
		CARGAR USUARIO POR SU ID
		loadUser()
	*/
	public function loadUser($login = FALSE) {
	  // Cargar datos
	  $sql = "SELECT u.user_id, u.user_name, u.user_email, u.user_rango, u.user_baneado, u.user_activo, u.user_lastlogin, u.user_lastactive, u.user_nextpuntos, s.session_id, s.session_user_id, s.session_ip, s.session_time, s.session_autologin FROM u_sessions s, u_miembros u WHERE s.session_id = '{$this->session->ID}' AND u.user_id = s.session_user_id";
	  $query = db_exec([__FILE__, __LINE__], 'query', $sql);
	  $this->info = db_exec('fetch_assoc', $query);
	  // Existe el usuario?
	  if(!isset($this->info['user_id'])) return FALSE;		
		/* ES MIEMBRO */
		$this->loadUserRango();
		// NOMBRE
		$this->nick = $this->info['user_name'];
		$this->uid = $this->info['user_id'];
	  	$this->is_banned = $this->info['user_baneado'];
	  	$this->avatar = $this->Avatar->get($this->uid);
		// ULTIMA ACCION
		db_exec([__FILE__, __LINE__], 'query', "UPDATE u_miembros SET user_lastactive = {$this->Junk->setTime()} WHERE user_id = {$this->uid}");
	  // Si ha iniciado sesión cargamos estos datos.
	  if($login) {
		 	// Last login
			db_exec([__FILE__, __LINE__], 'query', "UPDATE u_miembros SET user_lastlogin = {$this->session->time_now} WHERE user_id = {$this->uid}");
		 	// REGISTAR IP
		 	db_exec([__FILE__, __LINE__], 'query', "UPDATE u_miembros SET user_last_ip = {$this->session->ip_address} WHERE user_id = {$this->uid}");
	  }
	  // Borrar variable session
	  //unset($this->session);
	}
	
	/*
		getUserBanned()
	*/
	public function getUserBanned(){
		//
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT susp_termina FROM u_suspension WHERE user_id = '{$this->uid}' LIMIT 1"));
		if($data === null) return '';
		//
		$now = time();
		//
		if((int)$data['susp_termina'] > 1 && (int)$data['susp_termina'] < $now){
			db_exec([__FILE__, __LINE__], 'query', 'UPDATE u_miembros SET user_baneado = 0 WHERE user_id = ' . $this->uid);
			db_exec([__FILE__, __LINE__], 'query', 'DELETE FROM u_suspension WHERE user_id = ' . $this->uid);
			return false;
		} else return $data;
	}

	/*
		getUserID($tsUsername)
	*/
	public function getUserID(string $tsUser = ''): int {
		$username = $this->tsCore->setSecure($tsUser);
		return (int)db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT user_id FROM u_miembros WHERE user_name = '$username' LIMIT 1"))['user_id'] ?? 0;
	}

	/*
		getUserName($user_id)
	*/
	public function getUserName($user_id): string {
		return db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT user_name FROM u_miembros WHERE user_id = $user_id LIMIT 1"))['user_name'];
	}

	/**
	 * @name iFollow
	 * @access public
	 * @param int
	 * @return void
	 */
	public function iFollow(int $user_id = 0): bool {
		# SIGO A ESTE USUARIO
		return db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', "SELECT follow_id FROM u_follows WHERE f_id = $user_id AND f_user = {$this->uid} AND f_type = 1 LIMIT 1")) > 0;
	}

	/*
		getUsuarios()
	*/
	public function getUsuarios(): array {
		// FILTROS <| |>
		$is_online = (time() - ($this->tsCore->settings['c_last_active'] * 60));
		$is_inactive = (time() - (($this->tsCore->settings['c_last_active'] * 60) * 2)); // DOBLE DEL ONLINE
		// ONLINE?
		if($_GET['online'] == 'true'){
			$w_online = 'AND u.user_lastactive > '.$is_online.'';
		}
		// CON FOTO
		if($_GET['avatar'] == 'true'){
			$w_avatar = 'AND p.p_avatar = 1';
		}
		// SEXO
		if(!empty($_GET['sexo'])){
			$sex = ($_GET['sexo'] == 'f') ? 0 : 1;
			$w_sex = '&& p.user_sexo = \''.$sex.'\'';
		}
		// PAIS
		if(!empty($_GET['pais'])){
			$pais = $this->tsCore->setSecure($_GET['pais']);
			$w_pais = '&& p.user_pais = \''.$pais.'\'';
		}
		// STAFF
		if(!empty($_GET['rango'])){
			$rango = (int)$_GET['rango'];
			$w_rango = '&& u.user_rango = '.$rango.'';
		}
		// TOTAL Y PAGINAS
		$query = db_exec([__FILE__, __LINE__], 'query', 'SELECT COUNT(u.user_id) AS total FROM u_miembros AS u LEFT JOIN u_perfil AS p ON u.user_id = p.user_id WHERE u.user_activo = \'1\' && u.user_baneado = \'0\' '.$w_online.' '.$w_avatar.' '.$w_sex.' '.$w_pais.' '.$w_rango);
		$total = db_exec('fetch_assoc', $query);
		$total = $total['total'];
		
		$pages = $this->tsCore->getPagination($total, 12);
		// CONSULTA
		$query = db_exec([__FILE__, __LINE__], 'query', 'SELECT u.user_id, u.user_name, p.user_pais, p.user_sexo, p.p_avatar, p.p_mensaje, u.user_rango, u.user_puntos, u.user_comentarios, u.user_posts, u.user_lastactive, u.user_baneado, r.r_name, r.r_color, r.r_image FROM u_miembros AS u LEFT JOIN u_perfil AS p ON u.user_id = p.user_id LEFT JOIN u_rangos AS r ON r.rango_id = u.user_rango WHERE u.user_activo = \'1\' && u.user_baneado = \'0\' '.$w_online.' '.$w_avatar.' '.$w_sex.' '.$w_pais.' '.$w_rango.'  ORDER BY u.user_id DESC LIMIT '.$pages['limit']);
		// PARA ASIGNAR SI ESTA ONLINE HACEMOS LO SIGUIENTE
		while($row = db_exec('fetch_assoc', $query)){
			if($row['user_lastactive'] > $is_online) $row['status'] = array('t' => 'Online', 'css' => 'online');
			elseif($row['user_lastactive'] > $is_inactive) $row['status'] = array('t' => 'Inactivo', 'css' => 'inactive');
			else $row['status'] = array('t' => 'Offline', 'css' => 'offline');
			// RANGO
			$row['rango'] = array('title' => $row['r_name'], 'color' => $row['r_color'], 'image' => $row['r_image']);
			// CARGAMOS
			$data[] = $row;
		}
		
		// ACTUALES
		$total = explode(',',$pages['limit']);
		$total = ($total[0]) + count($data);
		//
		return array('data' => $data, 'pages' => $pages, 'total' => $total);
	}

}