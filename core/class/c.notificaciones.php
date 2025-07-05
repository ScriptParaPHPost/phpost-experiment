<?php

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
 * Aplicando Single Responsibility
*/

if (!defined('PHPOST_CORE_LOADED')) 
	exit('Acceso denegado: ¡No puedes acceder este script directamente!');

class tsNotificaciones {

	protected tsUser $tsUser;

	protected tsCore $tsCore;

	protected Junk $Junk;

	protected Avatar $Avatar;

	/**
	  * @name notificaciones 
	  * @access public
	  * @info NUMERO DE NOTIFICACIONES NUEVAS
	  **/
	public $notificaciones = 0;
	 /**
	  * @name avisos
	  * @access public
	  * @info NUMERO DE AVISOS/ALERTAS
	  */
	 public $avisos = 0;    
	 /**
	  * @name monitor
	  * @access private
	  * @info ORACIONES PARA CADA NOTIFICACION
	  **/
	 private $monitor = [];
	 /**
	  * @name show_type
	  * @access public
	  * @info COMO MOSTRAREMOS LAS NOTIFICACIONES -> AJAX/NORMAL
	  **/
	  public $show_type = 1;

	/*
		constructor()
	*/
	public function __construct(tsUser $deps) {
		if (!$deps) {
			throw new InvalidArgumentException('Todas las dependencias son obligatorias.');
		}
		$this->tsUser = $deps;
		$this->tsCore = $deps->getCore();
   	$this->Junk = $deps->getJunk();
   	$this->Avatar = $deps->getAvatar();
		// VISITANTE?
		if(empty($this->tsUser->is_member)) return false;
		// NOTIFICACIONES
		$this->notificaciones = $this->countNotificationsAlerts('notificaciones');
		// AVISOS
		$this->avisos = $this->countNotificationsAlerts('avisos');
	}
	/**
	 * name countNotificationsAlerts()
	 * @access private
	 * @param string
	 * @return int
	 */
	private function countNotificationsAlerts(string $type = ''): int {
		if ($type === 'notificaciones') {
			$field = 'not_id';
			$table = 'u_monitor';
			$condition = 'not_menubar > 0';
		} else {
			$field = 'av_id';
			$table = 'u_avisos';
			$condition = 'av_read = 0';
		}
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], "query", "SELECT COUNT($field) as total FROM $table WHERE user_id = {$this->tsUser->uid} AND $condition"));
		return (int)$data['total'] ?? 0;
	}
	
	private function monitorItem($text, $textCode, $css, $extra = '') {
		return [
			'text' => is_array($text) ? $text : [$text],
			'code' => $textCode,
			'icon' => $css,
			'extra' => $extra,
		];
	}

	/**
	 * @name makeMonitor
	 * @access private
	 * @params none
	 * @return none
	 */
	private function makeMonitor() {
		$this->monitor = [
			1  => $this->monitorItem('agregó a favoritos tu', 'post', 'star'),
			2  => $this->monitorItem(['comentó tu', '{{REPLACE}} nuevos comentarios en tu'], 'post', 'comment_post'),
			3  => $this->monitorItem('dejó {{REPLACE}} puntos en tu', 'post', 'points'),
			4  => $this->monitorItem('te está siguiendo', 'Seguir a este usuario', 'follow'),
			5  => $this->monitorItem('creó un nuevo', 'post', 'post'),
			6  => $this->monitorItem(['te recomienda un', '{{REPLACE}} usuarios te recomiendan un'], 'post', 'share'),
			7  => $this->monitorItem(['comentó en un', '{{REPLACE}} nuevos comentarios en el'], 'post', 'blue_ball', 'que sigues'),
			8  => $this->monitorItem(['votó {{REPLACE}} tu', '{{REPLACE}} nuevos votos a tu'], 'comentario', 'voto_'),
			9  => $this->monitorItem(['respondió tu', '{{REPLACE}} nuevas respuestas a tu'], 'comentario', 'comment_resp'),
			10 => $this->monitorItem('subió una nueva', 'foto', 'photo'),
			11 => $this->monitorItem(['comentó tu', '{{REPLACE}} nuevos comentarios en tu'], 'foto', 'photo'),
			12 => $this->monitorItem('publicó en tu', 'muro', 'wall_post'),
			13 => $this->monitorItem(['comentó', '{{REPLACE}} nuevos comentarios en'], 'publicación', 'w_comment', 'comentó'),
			14 => $this->monitorItem(['le gusta tu', 'A {{REPLACE}} personas les gusta tu'], ['publicación','comentario'], 'w_like'),
			15 => $this->monitorItem('Recibiste una medalla', '', 'medal'),
			16 => $this->monitorItem('Tu post recibió una medalla', '', 'medal'),
			17 => $this->monitorItem('Tu foto recibió una medalla', '', 'medal'),
		];
	}


	/**
	 * @name setAviso
	 * @access public
	 * @param int, string, string
	 * @return bool
	 * @info ENVIA UN AVISO/ALERTA
	*/
	public function setAviso(int $user_id = 0, string $subject = '(sin asunto)', string $body = '', int $type = 0){
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT user_baneado FROM u_miembros WHERE user_id = $user_id LIMIT 1"));
		# NO PODEMOS ENVIAR A UN USUARIO BANEADO
		if((int)$data['user_baneado'] === 1 || empty($body)) return true;
		# INSERTAMOS EL AVISO
		$body = $this->tsCore->setSecure($body);
		$subject = $this->tsCore->setSecure($subject);
		return (db_exec([__FILE__, __LINE__], 'query', "INSERT INTO u_avisos (user_id, av_subject, av_body, av_date, av_type) VALUES (\$user_id, '$subject', '$body', {$this->Junk->setTime()}, $type)"));
	}

	/**
	 * @name getAvisos
	 * @access public
	 * @param none
	 * @return array
	 * @info OBTIENE LOS MENSAJES Y ALERTAS DEL USUARIO
	 */
	public function getAvisos() {
		return result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT av_id, user_id, av_subject, av_body, av_date, av_read, av_type FROM u_avisos WHERE user_id = {$this->tsUser->uid}"));
	}

	/**
	 * @name readAviso
	 * @access public
	 * @param int
	 * @return array
	 * @info ONTIENE UN AVISO
	 */
	public function readAviso(int $av_id = 0){
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT av_id, user_id FROM u_avisos WHERE av_id = $av_id"));
		# RETURN
		if(empty($data['av_id']) || $data['user_id'] != $this->tsUser->uid && !$this->tsUser->is_admod) {
			return 'El aviso no existe';
		}
		db_exec([__FILE__, __LINE__], 'query', "UPDATE u_avisos SET av_read = 1 WHERE av_id = $av_id");
		$this->avisos = $this->avisos - 1;
		return $data;
	}

	/**
	 * @name delAviso
	 * @access public
	 * @param int
	 * @return bool
	 * @info ELIMINA UN AVISO
	 */
	public function delAviso(int $av_id = 0): bool {
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT user_id FROM u_avisos WHERE av_id = $av_id"));
	  	# RETURN
	  	if(empty($data['user_id']) || $data['user_id'] != $this->tsUser->uid && !$this->tsUser->is_admod) return false;
	  	db_exec([__FILE__, __LINE__], 'query', "DELETE FROM u_avisos WHERE av_id = $av_id");
		return true;
	}

	/**
	 * @name limitsNotifications
	 * @access private
	 * @param int
	 * @return void
	*/
	private function limitsNotifications(int $uid = 0) {
		$data = result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT not_id FROM u_monitor WHERE user_id = $uid ORDER BY not_id DESC"));
		$ntotal = count($data);
		$del_not_id = $ntotal > 0 ? (int)$data[$ntotal - 1]['not_id'] : 0;
		//
		$max_nots = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT c_max_nots FROM w_config_limits WHERE tscript_id = 1"))['c_max_nots'];
		// ELIMINAR NOTIFICACIONES?
		if($ntotal > (int)$max_nots){
			db_exec([__FILE__, __LINE__], 'query', "DELETE FROM u_monitor WHERE not_id = $del_not_id");
		}
	}

	/**
	 * @name setNotificacion
	 * @access public
	 * @param int
	 * @return void
	*/
	public function setNotificacion(int $type = 0, int $user_id = 0, int $obj_user = 0, int $obj_uno = 0, int $obj_dos = 0, int $obj_tres = 0){
		# NO SE MOSTRARA MI PROPIA ACTIVIDAD
		if($user_id !== $this->tsUser->uid) {
			# VERIFICA SI ESTE USUARIO ADMITE NOTIFICACIONES DEL TIPO $type
			$allow = $this->allowNotifi($type, $user_id);
			if(empty($allow)) return true;
			// VERIFICAR CUANTAS NOTIFICACIONES DEL MISMO TIPO Y EN POCO TIEMPO TENEMOS
			$tiempo = time() - 3600; //  HACE UNA HORA
			$not_data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT not_id FROM u_monitor WHERE user_id = $user_id AND obj_uno = $obj_uno AND obj_dos = $obj_dos AND not_type = $type AND not_date > $tiempo AND not_menubar > 0 ORDER BY not_id DESC LIMIT 1"));
			// COMPROBAR LIMITE DE NOTIFICACIONES
			$this->limitsNotifications($user_id);
			// ACTUALIZAMOS / INSERTAMOS
			if(!empty($not_data['not_id']) && $type !== 4) {
				if(db_exec([__FILE__, __LINE__], 'query', "UPDATE u_monitor SET obj_user = $obj_user, not_date = {$this->Junk->setTime()}, not_total = not_total + 1 WHERE not_id = {$not_data['not_id']}")) return true;
			} else {
				if(db_exec([__FILE__, __LINE__], 'query', "INSERT INTO u_monitor (user_id, obj_user, obj_uno, obj_dos, obj_tres, not_type, not_date) VALUES ($user_id, $obj_user, $obj_uno, $obj_dos, $obj_tres, $type, {$this->Junk->setTime()})")) return true;   
			}
		}
	}

	/**
	 * @name setFollowNotificacion
	 * @access public
	 * @param int $nType Tipo de notificación
	 * @param int $fType Tipo de seguimiento (1=usuario, 2=post)
	 * @param int $uid Usuario que genera la acción
	 * @param int $oUno Objeto 1 (puede ser post_id, por ejemplo)
	 * @param int $oDos Objeto 2 (extra)
	 * @param array $excluir Lista de usuarios a no notificar
	 * @return bool
	 */
	public function setFollowNotificacion(int $nType = 0, int $fType = 0, int $uid = 0, int $oUno = 0, int $oDos = 0, array $excluir = []): bool {
		if ($nType === 0 || $fType === 0 || $uid === 0) return false;
		$fID = match($fType) {
			1 => $uid,
			2 => $oUno,
			default => null
		};
		if (is_null($fID)) return false;
		$seguidores = result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT f_user FROM u_follows WHERE f_id = $fID AND f_type = $fType"));
		foreach ($seguidores as $follower) {
			$seguidor = (int)$follower['f_user'];
			if (!in_array($seguidor, $excluir) && $seguidor !== $uid) {
				$this->setNotificacion($nType, $seguidor, $uid, $oUno, $oDos);
			}
		}
		return true;
	}

	/**
	 * @name setMuroRepost
	 * @access public
	 * @param int $pub_id Id de la publicación
	 * @param int $p_user Dueño del muro
	 * @param int $p_user_pub Autor original del post
	 * @return void
	 * @info Notifica cuando alguien comenta una publicación en el muro.
	 */
	public function setMuroRepost(int $pub_id = 0, int $p_user = 0, int $p_user_pub = 0): void {
		$uid = (int) $this->tsUser->uid;
		$pub_id = (int) $pub_id;
		$p_user = (int) $p_user;
		$p_user_pub = (int) $p_user_pub;
		// Usuarios que ya comentaron en la publicación (excluyendo al actual y al dueño del muro)
		$data = result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT DISTINCT c_user FROM u_muro_comentarios WHERE pub_id = $pub_id AND c_user NOT IN ($uid, $p_user)"));
		$enviados = [];
		foreach ($data as $comentario) {
			$c_user = (int) $comentario['c_user'];
			if (!isset($enviados[$c_user])) {
				$this->setNotificacion(13, $c_user, $uid, $pub_id, 3); // Comentarios previos
				$enviados[$c_user] = true;
			}
		}
		// Dueño del muro
		if ($p_user !== $uid && !isset($enviados[$p_user])) {
			$this->setNotificacion(13, $p_user, $uid, $pub_id, 1);
			$enviados[$p_user] = true;
		}
		// Autor original (si no es el mismo que el dueño del muro)
		if ($p_user_pub !== $p_user && $p_user_pub !== $uid && !isset($enviados[$p_user_pub])) {
			$this->setNotificacion(13, $p_user_pub, $uid, $pub_id, 2);
		}
	}

	private function getNotificacionesStats() {
		# ESTADÍSTICAS
		return [
			'stats' => [
				'posts' => db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', "SELECT follow_id FROM u_follows WHERE f_user = {$this->tsUser->uid} && f_type = 3")),
				'seguidores' => db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', "SELECT follow_id FROM u_follows WHERE f_id = {$this->tsUser->uid} && f_type = 1")),
				'siguiendo' => db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', "SELECT follow_id FROM u_follows WHERE f_user = {$this->tsUser->uid} && f_type = 1"))
			]
		];
	}
	/**
	 * @name getNotificaciones
	 * @access public
	 * @param int
	 * @return array
	 * @info CREAR UN ARRAY CON LAS NOTIFICAIONES DEL USUARIO
	*/
	public function getNotificaciones(bool $unread = false): array {
		$uid = (int) $this->tsUser->uid;
		$dataDos = [];
		$not_view = $unread ? '= 2' : '> 0';
		$not_del = $unread ? 1 : 0;
		if ($this->show_type === 1) {
			// Notificaciones para mostrar en menú (limitadas o no)
			$condition = ($this->notificaciones > 5 || $unread) ? "AND m.not_menubar $not_view" : '';
			$limit     = ($this->notificaciones > 5 || $unread) ? '' : 'LIMIT 5';
			$order     = "ORDER BY m.not_id DESC";
			$where     = "m.user_id = $uid $condition";
		} elseif ($this->show_type === 2) {
			// Notificaciones para monitor
			$order = "ORDER BY m.not_id DESC";
			$where = "m.user_id = $uid";
			// Stats del usuario
			$dataDos = $this->getNotificacionesStats();
			// Filtros activos
			$filtroRow = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT c_monitor FROM u_portal WHERE user_id = $uid LIMIT 1"));
			$filtros = explode(',', $filtroRow['c_monitor'] ?? '');
			foreach ($filtros as $val) {
				if ($val !== '') $dataDos['filtro'][(int)$val] = true;
			}
		}
		// Consulta principal de notificaciones
		$data = result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT m.*, u.user_name AS usuario FROM u_monitor AS m LEFT JOIN u_miembros AS u ON m.obj_user = u.user_id WHERE $where $order $limit"));
		// Actualizar estado según tipo
		if ($this->show_type === 1) {
			db_exec([__FILE__, __LINE__], 'query', "UPDATE u_monitor SET not_menubar = $not_del WHERE user_id = $uid AND not_menubar > 0");
		} elseif ($this->show_type === 2) {
			db_exec([__FILE__, __LINE__], 'query', "UPDATE u_monitor SET not_monitor = 0 WHERE user_id = $uid AND not_monitor = 1");
		}
		// Procesar textos
		$dataDos['data']  = $this->armNotificaciones($data);
		$dataDos['total'] = count($dataDos['data']);
		return $dataDos;
	}

	/**
	 * Arma las notificaciones a partir de un arreglo de datos base.
	 *
	 * @param array $array Datos base para generar las notificaciones
	 * @return array Notificaciones armadas
	 */
	private function armNotificaciones(array $array): array {
		$this->makeMonitor(); // Preparamos el monitor antes de empezar
		$data = [];

		foreach ($array as $val) {
			$notificacion = [];
			// Crear consulta con los datos base
			$sql = $this->makeConsulta($val);
			// Ejecutar consulta y obtener datos adicionales si corresponde
			if (is_array($sql)) {
				$notificacion = $sql;
			} elseif (is_string($sql) && $query = db_exec([__FILE__, __LINE__], 'query', $sql)) {
				$fetched = db_exec('fetch_assoc', $query);
				if (is_array($fetched)) {
					$notificacion = $fetched;
				}
			}
			// Mezclar datos originales con los obtenidos
			if (!empty($notificacion)) {
				$notificacion = array_merge($notificacion, $val);
				// Generar la oración de notificación
				$oracion = $this->makeOracion($notificacion);
				if (!empty($oracion)) {
					$data[] = $oracion;
				}
			}
		}
		return $data;
	}

	/**
	 * @name makeConsulta
	 * @access private
	 * @param array
	 * @return string
	 * @info RETORNA UNA CONSULTA DEPENDIENDO EL TIPO DE NOTIFICACION
	*/
	public function makeConsulta(array $data = []) {
		$type   = (int)$data['not_type'];
		$objUno = (int)$data['obj_uno'];
		$objDos = (int)$data['obj_dos'];
		$objUser = (int)$data['obj_user'];
		return match ($type) {
			1, 2, 3, 5, 6, 7, 8, 9 => $this->getPostData($objUno),
			4	=> ['follow' => $this->tsUser->iFollow($objUser)],
			12	=> $this->getMuroPublicacion($objUno),
			13	=> $this->getMuroRepost($objUno, $objUser),
			14	=> $this->getComentarioMuro($objUno, $objDos),
			15	=> $this->getMedalla($objUno),
			16	=> $this->getMedallaEnPost($objUno, $objDos),
			17	=> $this->getMedallaEnFoto($objUno, $objDos),
			default	=> null,
		};
	}

	private function getPostData(int $postId): string {
		return "SELECT p.post_id, p.post_user, p.post_title, c.c_seo FROM p_posts AS p LEFT JOIN p_categorias AS c ON p.post_category = c.cid WHERE p.post_id = $postId LIMIT 1";
	}

	private function getMuroPublicacion(int $pubId): string {
		return "SELECT p.pub_id, u.user_name FROM u_muro AS p LEFT JOIN u_miembros AS u ON p.p_user_pub = u.user_id WHERE p.pub_id = $pubId LIMIT 1";
	}

	private function getMuroRepost(int $pubId, int $objUser): array {
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT p.pub_id, p.p_user, p.p_user_pub, u.user_name FROM u_muro AS p LEFT JOIN u_miembros AS u ON p.p_user = u.user_id WHERE p.pub_id = $pubId LIMIT 1"));
		$data['p_user_resp'] = $objUser;
		$data['p_user_name'] = (string)$data['user_name'];
		$data['user_name']   = $this->tsUser->getUserName($objUser);
		return $data;
	}

	private function getComentarioMuro(int $cid, $obj): string {
		return ($obj === 2) ? "SELECT pub_id AS obj_uno, c_body FROM u_muro_comentarios WHERE cid = $cid" : ['value' => 'hack'];
	}

	private function getMedalla(int $medalId): string {
		return "SELECT medal_id, m_title, m_image FROM w_medallas WHERE medal_id = $medalId LIMIT 1";
	}

	private function getMedallaEnPost(int $medalId, int $postId): string {
		return "SELECT p.post_id, p.post_title, c.c_seo, m.medal_id, m.m_title, m.m_image, a.medal_for FROM w_medallas_assign AS a LEFT JOIN p_posts AS p ON p.post_id = a.medal_for LEFT JOIN p_categorias AS c ON c.cid = p.post_category LEFT JOIN w_medallas AS m ON m.medal_id = a.medal_id WHERE m.medal_id = $medalId AND p.post_id = $postId LIMIT 1";
	}

	private function getMedallaEnFoto(int $medalId, int $fotoId): string {
		return "SELECT f.foto_id, f.f_title, f.f_user, m.medal_id, m.m_title, m.m_image, a.medal_for, u.user_id, u.user_name FROM w_medallas_assign AS a LEFT JOIN f_fotos AS f ON f.foto_id = a.medal_for LEFT JOIN u_miembros AS u ON u.user_id = f.f_user LEFT JOIN w_medallas AS m ON m.medal_id = a.medal_id WHERE m.medal_id = $medalId AND f.foto_id = $fotoId LIMIT 1";
	}

	public function toStrong(string $replace = '', string $text = '') {
		return str_replace('{{REPLACE}}', "<strong>$replace</strong>", $text);
	}

	/**
	 * @name makeOracion
	 * @access private
	 * @param array, int, int
	 * @return array
	 * @info RETORNA LAS ORACIONES A MOSTRAR EN EL MONITOR
	*/
	private function makeOracion(array $data = []){
		# LOCALES
		$ShowType = (int)$this->show_type;
		$site_url = $this->tsCore->settings['url'];
		$no_type = (int)$data['not_type'];
		$LnText = $this->monitor[$no_type]['text_code'];
		$textExtra = $ShowType === 1 ? '' : ' '.$LnText;
		$lineText = is_array($LnText) ? $LnText[$data['obj_dos']-1] : $LnText;
		//
		$oracion = [
			'text' => $this->monitor[$no_type]['text'],
			'unread'	=> $data['not_' . ($ShowType === 1 ? 'menubar' : 'monitor')],
			'style' 	=> $this->monitor[$no_type]['css'],
			'date' 	=> $data['not_date'],
			'user' 	=> $data['usuario'],
			'avatar' => $this->Avatar->get((int)$data['obj_user']),
			'total' 	=> (int)$data['not_total']
		];
		# CON UN SWITCH ESCOGEMOS QUE ORACION CONSTRUIR
		switch($no_type){
			case 1: case 3: case 5:
				$oracion['text'] .= $textExtra;
				if($no_type === 3) $oracion['text'] = $this->toStrong($data['obj_dos'], $oracion['text']);
				$oracion['link'] = $this->UrlBuilder($data, 'post');
				$oracion['ltext'] = $ShowType === 1 ? $lineText : $data['post_title'];
				$oracion['ltit'] = $ShowType === 1 ? $data['post_title'] : '';
			break;
			// FOLLOW
			case 4:
				if(!$data['follow'] && $ShowTyp === 2) {
					$oracion['link'] = '#" onclick="notifica.follow(\'user\', '.$data['obj_user'].', notifica.userInMonitorHandle, this)';
					$oracion['ltext'] = $this->monitor[$no_type]['text_code'];    
				}
			break;
			// PUEDEN SER MAS DE UNO
			case 2: case 6: case 7: case 8: case 9:
				// CUANTOS
				$no_total = (int)$data['not_total'];
				// MAS DE UNA ACCION
				$text = $this->monitor[$no_type]['text'][($no_total > 1 ? 1 : 0)].$textExtra;
				$oracion['text'] = ($no_total > 1) ? $this->toStrong($no_total, $text) : $text;
				// ¿ES MI POST?
				if((int)$data['post_user'] === (int)$this->tsUser->uid) {
					$oracion['text'] = str_replace('te recomienda un', 'ha recomendado tu', $oracion['text']);
				}
				// ID COMMENT
				if($no_type === 8 || $no_type === 9){
					$id_comment = '#comentario-'.$data['obj_dos'];
					// EXTRAS
					if($no_type === 8){
						$voto_type = ($data['obj_tres'] == 0) ? 'negativo' : 'positivo';
						$oracion['text'] = $this->toStrong($voto_type, $oracion['text']);
						$oracion['style'] = 'voto_'.$voto_type;
					}
				}
				//
				$oracion['link'] = $this->UrlBuilder($data, 'post', $id_comment);
				$oracion['ltext'] = $ShowType === 1 ? $lineText : $data['post_title'];
				$oracion['ltit'] = $ShowType === 1 ? $data['post_title'] : '';
			break;
			// PUBLICACION EN MURO
			case 12:
				$oracion['text'] = $this->monitor[$no_type]['text'].$textExtra;
				$oracion['link'] = $this->UrlBuilder($data, 'perfil', $this->tsUser->nick);
				$oracion['ltext'] = ($ShowType === 1) ? $lineText : $this->tsUser->nick;
				$oracion['ltit'] = ($ShowType === 1) ? $this->tsUser->nick : '';
			break;
			case 13:
				// DE QUIEN?
				$de = ((int)$this->tsUser->uid === (int)$data['p_user']) ? ' tu' : (((int)$data['p_user'] === (int)$data['p_user_resp']) ? ' su' : ' la publicaci&oacute;n de');
				// CUANTOS
				$no_total = (int)$data['not_total'];
				$text = $this->monitor[$no_type]['text'][($no_total > 1 ? 1 : 0)].$de.$textExtra;
				$oracion['text'] = ($no_total > 1) ? $this->toStrong($no_total, $text) : $text;
				//
				$oracion['link'] = $this->UrlBuilder($data, 'publicacion', $data['pub_id']);
				$oracion['ltext'] = ($ShowType === 1) ? $lineText : $this->tsUser->nick;
				$oracion['ltit'] = ($ShowType === 1) ? $this->tsUser->nick : '';
			break;
			case 14:
				// CUANTOS
				$no_total = (int)$data['not_total'];
				// MAS DE UNA ACCION
				if($no_total > 1) {
					$text = $this->monitor[$no_type]['text'][1].' '.$lineText;
					$oracion['text'] = $this->toStrong($no_total, $text);
				} else $oracion['text'] = $this->monitor[$no_type]['text'][0];
				//
				$oracion['text'] = ($ShowType == 1) ? $oracion['text'] : $oracion['text'].' '.$lineText;
				$oracion['link'] = $this->UrlBuilder($data, 'perfil', $this->tsUser->nick);
				$oracion['ltext'] = ($ShowType == 1) ? $lineText : substr($data['c_body'],0,20).'...';
				$oracion['ltit'] = ($ShowType == 1) ? substr($data['c_body'],0,20).'...' : '';
			break;
			case 15: case 16: case 17:
				$medalla = $this->UrlBuilder($data, 'medalla');

			case 15:
				$oracion['text'] = 'Recibiste una nueva <strong>medalla</strong> <img src="'.$medalla.'"/>';
			break;
			case 16:
				$oracion['text'] = 'Tu <a href="'.$this->UrlBuilder($data, 'post').'" title="'.$data['post_title'].'"><strong>post</strong></a> tiene una nueva <strong>medalla</strong> <img src="'.$medalla.'"/>';
			break;
			case 17:
				$oracion['text'] = 'Tu <a href="'.$this->UrlBuilder($data, 'foto').'" title="'.$data['f_title'].'"><strong>foto</strong></a> tiene una nueva <strong>medalla</strong> <img src="'.$medalla.'"/>';
			break;
		  }
		  # RETORNAMOS
		  return $oracion;
	 }
	/**
	  * @name setFollow
	  * @access public
	  * @param none
	  * @return string
	  * @info MANEJA EL SEGUIR USUARIO/POST
	*/
	public function setFollow() {
		global $tsActividad;
		// VARS
		$notType = 4; // NOTIFICACION
		$fw = $this->getFollowVars();
		$uid = (int)$this->tsUser->uid;
		// ANTI FLOOD
		$flood = $this->tsCore->antiFlood(false,'follow');
		if(strlen($flood) > 1) {
			$flood = str_replace('0: ','',$flood);
			return '1-'.$fw['obj'].'-0-'.$flood;
		}
		// YA EXISTE?
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT follow_id FROM u_follows WHERE f_user = $uid AND f_id = {$fw['obj']} AND f_type = {$fw['type']} LIMIT 1'"));
		// SEGUIR
		if(!empty($data['follow_id'])) return '2-'.$fw['obj'].'-0';
		if($uid === $fw['obj'] && (int)$fw['type'] === 1) return '1-'.$fw['obj'].'-0-No puedes seguirte a ti mismo.';
		if(!db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `u_follows` (`f_user`, `f_id`, `f_type`, `f_date`) VALUES ($uid, {$fw['obj']}, {$fw['type']}, {$this->Junk->setTime()})")) return '1-'.$fw['obj'].'-0-No se pudo completar la acci&oacute;n.';
		// MONITOR?
		if($fw['notUser'] > 0) $this->setNotificacion($notType, $fw['notUser'], $this->tsUser->uid);
		// CUANTOS?
		$total = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT COUNT(follow_id) AS total FROM u_follows WHERE f_id = {$fw['obj']} AND f_type = {$fw['type']}"));
		// ACTIVIDAD
		$ac_type = ((int)$fw['type'] === 1) ? 8 : 7;
		$tsActividad->setActividad($ac_type, $fw['obj']);
		// RESPUESTA
		return '0-'.$fw['obj'].'-'.$total['total'];
	}
	/**
	  * @name setUnFollow
	  * @access public
	  * @param none
	  * @return string
	  * @info MANEJA EL DEJAR DE SEGUIR UN USUARIO/POST
	*/
	public function setUnFollow(){
		$notType = 4; // NOTIFICACION
		$fw = $this->getFollowVars();
		// ANTI FLOOD
		$flood = $this->tsCore->antiFlood(false, 'follow');
		if(strlen($flood) > 1) {
			$flood = str_replace('0: ','',$flood);
			return '1-'.$fw['obj'].'-0-'.$flood;
		}
		// DEJAR DE SEGUIR
		if(!db_exec([__FILE__, __LINE__], 'query', "DELETE FROM u_follows WHERE f_user = {$this->tsUser->uid} AND f_id = {$fw['obj']} AND f_type = {$fw['type']}")) return '1-'.$fw['obj'].'-0-No se pudo completar la acci&oacute;n.';
		// CUANTOS?
		$total= db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', "SELECT follow_id FROM u_follows WHERE f_id = {$fw['obj']} AND f_type = {$fw['type']}"));
		// RESPUESTA
		return '0-'.$fw['obj'].'-'.$total;
	}

	/**
	 * @name getFollowVars
	 * @access private
	 * @return array
	 * @info Genera un array seguro con la información recibida por AJAX
	 */
	private function getFollowVars(): array {
		$type = $_POST['type'] ?? '';
		$obj  = isset($_POST['obj']) ? (int)$this->tsCore->setSecure($_POST['obj']) : 0;
		$return = [
			'sType'   => $type,
			'obj'     => $obj,
			'type'    => 0,
			'notUser' => 0
		];
		$return['type'] = match ($type) {
			'user' => 1,
			'post' => 2,
			default => 0
		};
		if ($return['type'] === 1) {
			$return['notUser'] = $obj;
		}
		return $return;
	}

	private function setPagination(string $query = '', int $max = 12): array {
		// PAGINAR
		$total = (int)db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', $query));
		$pages = $this->tsCore->getPagination($total, $max);
		$data['pages'] = $pages;
		$data['query'] = result_array(db_exec([__FILE__, __LINE__], 'query', "$query LIMIT {$pages['limit']}"));
		return $data;
	}
	/**
	  * @name getFollows
	  * @access public
	  * @param int
	  * @return array
	  * @info CARGA EN UN ARRAY LA INFORMACION DE LOS "FOLLOWs" DE UN USUARIO
	*/
	public function getFollows(string $type = '', int $user_id = 0): array {
		// VARS
		$user_id = empty($user_id) ? (int)$this->tsUser->uid : (int)$user_id;
		//
		switch($type){
			case 'seguidores':
			case 'siguiendo':
				$col = ($type === 'seguidores') ? "f.f_id" : "f.f_user";
				$query = "SELECT u.user_id, u.user_name, p.user_pais, p.p_mensaje, f.follow_id FROM u_miembros AS u LEFT JOIN u_perfil AS p ON u.user_id = p.user_id LEFT JOIN u_follows AS f ON p.user_id = f.f_user WHERE $col = {$user_id} AND f.f_type = 1 ORDER BY f.f_date DESC";
			break;
			case 'posts':
				$query = "SELECT f.f_id, p.post_user, p.post_title, u.user_name, c.c_seo, c.c_nombre, c.c_img FROM u_follows AS f LEFT JOIN p_posts AS p ON f.f_id = p.post_id LEFT JOIN u_miembros AS u ON u.user_id = p.post_user LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE f.f_user = {$user_id} AND f.f_type = 2 ORDER BY f.f_date DESC";
			break;
		}
		// PAGINAR
		$pagination = $this->setPagination($query);
		$data['pages'] = $pagination['pages'];
		if($type === 'seguidores') {
			foreach($pagination['query'] as $key => $val){
				$siguiendo = db_exec('fetch_assoc',db_exec([__FILE__, __LINE__], 'query', "SELECT follow_id FROM u_follows WHERE f_user = {$user_id} AND f_id = {$val['user_id']} AND f_type = 1"));
				$val['follow'] = (!empty($siguiendo['follow_id'])) ? 1 : 0;
				$data['data'][] = $val;
			}
		} else $data['data'] = $pagination['query'];
		//
		return $data;
	}
	/**
	  * @name setSpam
	  * @access public
	  * @param none
	  * @return string
	  * @info ESTA FUNCION ES PARA REALIZAR RECOMENDACIONES
	*/
	public function setSpam(){
		global $tsActividad;
		//
		$postid = (int)$_POST['postid'] ?? 0;
		// TIENE SEGUIDORES?
		$seguidores = db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', "SELECT follow_id FROM u_follows WHERE f_id = {$this->tsUser->uid} AND f_type = 1 LIMIT 1"));
		// YA LO HA RECOMENDADO?
		$recomendado = db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', "SELECT follow_id FROM u_follows WHERE f_id = $postid AND f_user = {$this->tsUser->uid} AND f_type = 3 LIMIT 1"));
		  
		if($seguidores < 1) return '0-Debes tener al menos un seguidor';
		if($recomendado > 0) return '0-No puedes recomendar el mismo post m&aacute;s de una vez.'; 
		//
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT post_user FROM p_posts WHERE post_id = $postid LIMIT 1"));
		//
		if((int)$this->tsUser->uid === (int)$data['post_user']) return '0-No puedes recomendar tus posts.';
		// GUARDAMOS EN FOLLOWS PUES ES LA RECOMENDACION PARA SU SEGUIDORES! xD
		db_exec([__FILE__, __LINE__], 'query', "INSERT INTO u_follows (f_id, f_user, f_type, f_date) VALUES ($postid, {$this->tsUser->uid}, 3, {$this->Junk->setTime()})");
		// NOTIFICAR
		if($this->setFollowNotificacion(6, 1, $this->tsUser->uid, $postid)) {
			$tsActividad->setActividad(4, $postid);
			return '1-La recomendaci&oacute;n fue enviada.';
		}
	}

	/**
	 * @name setFiltro
	 * @access public
	 * @param none
	 * @return bool
	 * @info GUARDA LOS FILTROS DE LA ACTIVIDAD
	*/
	public function setFiltro() {
		global $tsUser;
		//
		foreach ($_POST['fid'] as $key => $value) $fid[] = 'f'.$value;
		$filtros = join(',', $fid);
		# GUARDAR
		db_exec([__FILE__, __LINE__], 'query', "UPDATE u:portal SET c_monitor = '$filtros' WHERE user_id = {$tsUser->uid}");
		return true;
	}

	/**
	 * @name allowNotifi
	 * @access private
	 * @param int
	 * @return bool
	 * @info REVISA EN LA CONFIGURACION SI DESEA RESIBIR LA NOTIFICACION
	 */
	private function allowNotifi(int $type = 0, int $user_id = 0){
		# CONSULTAMOS
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT c_monitor FROM u_portal WHERE user_id = {$user_id} LIMIT 1"));
		# PROSESAMOS
		$filtro = 'f'.$type;
		$filtros = explode(',', $data['c_monitor']);
		# VERIFICAMOS
		return (is_array($filtros) AND in_array($filtro, $filtros)) ? false : true;
	}
}