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

class tsActividad {

	protected tsCore $tsCore;

	protected tsUser $tsUser;

	protected Junk $Junk;

	protected Avatar $Avatar;

	private array $actividad = [];

	# NO ES NESESARIO HACER ALGO EN EL CONSTRUCTOR
	public function __construct(tsUser $deps) {
		if (!$deps) {
			throw new InvalidArgumentException('Todas las dependencias son obligatorias.');
		}
		$this->tsUser = $deps;
		$this->tsCore = $deps->getCore();
   	$this->Junk = $deps->getJunk();
   	$this->Avatar = $deps->getAvatar();
	}

	/**
	 * @name makeActividad
	 * @access private
	 * @params none
	 * @return none
	 */
	private function makeActividad() {
		# ACTIVIDAD CON FORMATO | ID => array(TEXT, LINK, CSS_CLASS)
		$this->actividad = [
			// POSTS
			1 => ['text' => 'Cre&oacute; un nuevo post', 'css' => 'post'],
			2 => ['text' => 'Agreg&oacute; a favoritos el post', 'css' => 'star'],
			3 => ['text' => ['Dej&oacute;', 'puntos en el post'], 'css' => 'points'],
			4 => ['text' => 'Recomend&oacute; el post', 'css' => 'share'],
			5 => ['text' => ['Coment&oacute;', 'el post'], 'css' => 'comment_post'],
			6 => ['text' => ['Vot&oacute;', 'un comentario en el post'], 'css' => 'voto_'],
			7 => ['text' => 'Est&aacute; siguiendo el post', 'css' => 'follow_post'],
			// FOLLOWS
			8 => ['text' => 'Est&aacute; siguiendo a', 'css' => 'follow'],
			// FOTOS
			9 => ['text' => 'Subi&oacute; una nueva foto', 'css' => 'photo'],
			// MURO
			10 => [
				0 => ['text' => 'Public&oacute; en su', 'link' => 'muro', 'css' => 'status'],
				1 => ['text' => 'Coment&oacute; su', 'link' => 'publicaci&oacute;n', 'css' => 'w_comment'],
				2 => ['text' => 'Public&oacute; en el muro de', 'css' => 'wall_post'],
				3 => ['text' => 'Coment&oacute; la publicaci&oacute;n de', 'css' => 'w_comment']
			],
			11 => ['text' => 'Le gusta', 'css' => 'w_like',
				0 => ['text' => 'su', 'link' => 'publicaci&oacute;n'],
				1 => ['text' => 'su comentario'],
				2 => ['text' => 'la publicaci&oacute;n de'],
				3 => ['text' => 'el comentario'],
			]
		];
	}

	/**
	 * @name setActividad
	 * @access public
	 * @params none
	 * @return void
	 */
	public function setActividad(int $ac_type = 0, int $obj_uno = 0, int $obj_dos = 0) {
		# VARIABLES LOCALES
		$uid = (int)$this->tsUser->uid;
		$ac_date = $this->Junk->setTime();
		# BUSCAMOS ACTIVIDADES				
		$data = result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT `ac_id` FROM `u_actividad` WHERE user_id = (int)$uid ORDER BY ac_date DESC"));
		$ntotal = count($data ?? []);
		// ID DE ULTIMA NOTIFICACION
		$delid = (int)$data[$ntotal-1]['ac_id'];
		// ELIMINAR ACTIVIDADES?
		if($ntotal >= (int)$this->tsCore->settings['c_max_acts']) {
			db_exec([__FILE__, __LINE__], 'query', "DELETE FROM `u_actividad` WHERE `ac_id` = $delid");
		}
		# SE HACE UN CONTEO PROGRESIVO SI HACE ESTA ACCON MAS DE 1 VEZ AL DIA
		if((int)$ac_type === 5) {
			//
			$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT `ac_id`, `ac_date` FROM `u_actividad` WHERE user_id = $uid AND obj_uno = $obj_uno AND ac_type = $ac_type LIMIT 1'"));
			//
			$hace = $this->makeFecha($data['ac_date']);
			if($hace === 'today') {
				if(db_exec([__FILE__, __LINE__], 'query', "UPDATE `u_actividad` SET obj_dos = obj_dos + 1 WHERE ac_id = (int){$data['ac_id']} LIMIT 1")) return true;			
			}
		}
		# INSERCION DE DATOS        
		return (db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `u_actividad` (`user_id`, `obj_uno`, `obj_dos`, `ac_type`, `ac_date`) VALUES ($uid, $obj_uno, $obj_dos, $ac_type, $ac_date)"));
	}

	/**
	 * @name getActividad
	 * @access public
	 * @params int(3)
	 * @return array
	*/
	public function getActividad(int $user_id = 0, int $ac_type = 0, int $start = 0, $v_type = null): array {
		# CREAR ACTIVIDAD
		$this->makeActividad();
		# VARIABLES LOCALES
		$ac_type = ((int)$ac_type !== 0) ? " AND ac_type = $ac_type" : '';
		# CONSULTA
		$data = result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT `ac_id`, `user_id`, `obj_uno`, `obj_dos`, `ac_type`, `ac_date` FROM `u_actividad` WHERE user_id = $user_id$ac_type ORDER BY ac_date DESC LIMIT $start, 25"));
		# ARMAR ACTIVIDAD
		$actividad = $this->armActividad($data);
		# RETORNAR ACTIVIDAD
		return $actividad;
	}

	/**
	 * @name getActividadFollows
	 * @access public
	 * @param none
	 * @return array
	 */
	public function getActividadFollows(int $start = 0): array {
		# CREAR ACTIVIDAD
		$this->makeActividad();
		// SOLO MOSTRAREMOS LAS ULTIMAS 100 ACTIVIDADES
		if((int)$start > 90) return ['total' => '-1'];
		// SEGUIDORES
		$follows = result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT `f_id` FROM `u_follows` WHERE f_user = {$this->tsUser->uid} AND f_type = 1"));
		// ORDENAMOS 
		foreach($follows as $key => $val) $amigos[] = "'{$val['f_id']}'";
		// ME AGREGO A LA LISTA DE AMIGOS
		$amigos[] = $this->tsUser->uid;
		// CONVERTIMOS EL ARRAY EN STRING
		$amigos = implode(', ',$amigos);
		// OBTENEMOS LAS ULTIMAS PUBLICACIONES
		$data = result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT a.*, u.user_name AS usuario FROM u_actividad AS a LEFT JOIN u_miembros AS u ON a.user_id = u.user_id WHERE a.user_id IN($amigos) ORDER BY ac_date DESC LIMIT (int)$start, 25"));
		# ARMAR ACTIVIDAD
		if(empty($data)) return 'No hay actividad o no sigues a ning&uacute;n usuario.';
		$actividad = $this->armActividad($data);
		# RETORNAR ACTIVIDAD
		return $actividad;
	}

	/**
	 * @name delActividad
	 * @access public
	 * @param none
	 * @return string
	*/
	public function delActividad(): string {
		# VARIABLES LOCALES
		$ac_id = (int)$_POST['acid'];
		# CONSULTAS		
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT user_id FROM u_actividad WHERE ac_id = $ac_id LIMIT 1"));
		# COMPROBAMOS
		if((int)$data['user_id'] === (int)$this->tsUser->uid) {
			if(db_exec([__FILE__, __LINE__], 'query', "DELETE FROM `u_actividad` WHERE ac_id = (int)$ac_id")) 
				return '1: Actividad borrada';
		}
		//
		return '0: No puedes borrar esta actividad.';
	}

	/**
	 * @name armActividad
	 * @access private
	 * @params array
	 * @return array
	 */
	private function armActividad(array $data = []): array {
		# VARIABLES LOCALES
		$actividad = [
			'total' => (empty($data) ? 0 : count($data)),
			'data' => [
				'today' => ['title' => 'Hoy', 'data' => []],
				'yesterday' => ['title' => 'Ayer', 'data' => []],
				'week' => ['title' => 'D&iacute;as Anteriores', 'data' => []],
				'month' => ['title' => 'Semanas Anteriores', 'data' => []],
				'old' => ['title' => 'Actividad m&aacute;s antigua', 'data' => []]
			]
		];
		# PARA CADA VALOR CREAR UNA CONSULTA
		foreach($data as $key => $val){
			// CREAR CONSULTA
			$sql = $this->makeConsulta($val);
			// CONSULTAMOS
			$dato = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', $sql));
			//
			if(!empty($dato)) {
				// AGREGAMOS AL ARRAY ORIGINAL
				$dato = array_merge($dato, $val);
				// ARMAMOS LOS TEXTOS
				$oracion = $this->makeOracion($dato);
				// DONDE PONERLO?
				$ac_date = $this->makeFecha($val['ac_date']);
				// PONER
				$actividad['data'][$ac_date]['data'][] = $oracion;
			}
		}
		#RETORNAMOS LOS VALORES
		return $actividad;
	}

	/**
	 * @name makeConsulta
	 * @access private
	 * @params array
	 * @return string/array
	*/
	private function makeConsulta(array $data = []):string {
		#
		$obj_uno = (int)$data['obj_uno'];
		$obj_dos = (int)$data['obj_dos'];
		# CON UN SWITCH ESCOGEMOS LA CONSULTA APROPIADA
		switch((int)$data['ac_type']){
			// DEL TIPO 1 al 7 USAMOS LA MISMA CONSULTA
			case 1: case 2: case 3: case 4: case 5: case 6: case 7:
				return "SELECT p.post_id, p.post_title, c.c_seo FROM p_posts AS p LEFT JOIN p_categorias AS c ON p.post_category = c.cid WHERE p.post_id = $obj_uno LIMIT 1";
			break;
			// SIGUIENDO A...
			case 8:
				return "SELECT user_id AS avatar, user_name FROM u_miembros WHERE user_id = $obj_uno LIMIT 1";
			break;
			// SUBIO UNA FOTO
			case 9:
				return "SELECT f.foto_id, f.f_title, u.user_name FROM f_fotos AS f LEFT JOIN u_miembros AS u ON f.f_user = u.user_id WHERE f.foto_id = $obj_uno LIMIT 1";
			break;
			// PUBLICACION EN EL MURO & LE GUSTA
			case 10: case 11:
				if($obj_dos === 0 || $obj_dos === 2) {
					return "SELECT p.pub_id, u.user_name FROM u_muro AS p LEFT JOIN u_miembros AS u ON p.p_user = u.user_id WHERE p.pub_id = $obj_uno LIMIT 1";
				} else {
					return "SELECT c.pub_id, c.c_body, u.user_name FROM u_muro_comentarios AS c LEFT JOIN u_muro AS p ON c.pub_id = p.pub_id LEFT JOIN u_miembros AS u ON p.p_user = u.user_id WHERE cid = $obj_uno LIMIT 1";
				}
			break;
		}
	}

	/**
	 * @name makeOracion
	 * @access private
	 * @params array
	 * @return array
	*/
	private function makeOracion(array $data = []) {
		# VARIABLES LOCALES
		$ac_type = (int)$data['ac_type'];
		$oracion['id'] = (int)$data['ac_id'];
		$oracion['style'] = $this->actividad[$ac_type]['css'];
		$oracion['date'] = (int)$data['ac_date'];
		$oracion['user'] = (int)$data['usuario'];
		$oracion['uid'] = (int)$data['user_id'];
		$obj_dos = (int)$data['obj_dos'];
		$text_type = $this->actividad[$ac_type]['text'];
		# CON UN SWITCH ESCOGEMOS QUE ORACION CONSTRUIR
		switch($ac_type){
			# DEL TIPO 1-2, 4 y 7 USAMOS LA MISMA
			case 1: case 2: case 4: case 7:
				$oracion['text'] = $text_type;
				$oracion['link'] = $this->Junk->UrlBuilder($data);
				$oracion['ltext'] = $data['post_title'];
			break;
			# DEL TIPO 3, 5 y 6 USAMOS EL MISMO
			case 3: case 5: case 6:
				$extra_text = match ($ac_type) {
					3 => $obj_dos,
					5 => ($obj_dos > 0 ? '' : ($obj_dos + 1).' veces'),
					default => ($obj_dos > 0 ? 'negativo' : 'positivo'),
				};
				//
				$oracion['text'] = "{$text_type[0]} <strong>{$extra_text}</strong> {$text_type[1]}";
				$oracion['link'] = $this->Junk->UrlBuilder($data);
				$oracion['ltext'] = $data['post_title'];
				// ESTILO
				$oracion['style'] = ($ac_type === 6) ? "voto_$extra_text" : $oracion['style'];
			break;
			# ESTA SIGUIENDO A..
			case 8:
				// AVATARES
				$img_uno = '<img class="square object-cover aspect-square" alt="avatar usuario" width="16" height="16" src="'.$this->Avatar->get($data['user_id']).'"/>';
				$img_dos = '<img class="square object-cover aspect-square" alt="avatar usuario" width="16" height="16" src="'.$this->Avatar->get($data['avatar']).'"/>';
				// ORACION
				$oracion['text'] = "$img_uno $text_type $img_dos";
				$oracion['link'] = $this->Junk->UrlBuilder($data, 'perfil');
				$oracion['ltext'] = $data['user_name'];
				$oracion['style'] = '';
			break;
			# SUBIO NUEVA FOTO
			case 9:
				$oracion['text'] = $text_type;
				$oracion['link'] = $this->Junk->UrlBuilder($data, 'fotos');
				$oracion['ltext'] = $data['f_title'];
			break;
			# MURO POSTS
			case 10:
				// SEC TYPE
				$link_text = $this->actividad[$ac_type][$obj_dos]['link'];
				//
				$oracion['text'] = $this->actividad[$ac_type][$obj_dos]['text'];
				$oracion['link'] = $this->Junk->UrlBuilder($data, 'publicacion');
				$oracion['ltext'] = empty($link_text) ? $data['user_name'] : $link_text;
				$oracion['style'] = $this->actividad[$ac_type][$obj_dos]['css'];
			break;
			# LIKES
			case 11:
				// SEC TYPE
				$link_text = $this->actividad[$ac_type][$obj_dos]['link'];
				//
				$oracion['text'] = "$text_type {$this->actividad[$ac_type][$obj_dos]['text']}";
				$oracion['link'] = $this->Junk->UrlBuilder($data, 'perfil', "?pid={$data['pub_id']}");
				if($obj_dos === 0 || $obj_dos === 2) {
					$oracion['ltext'] = empty($link_text) ? $data['user_name'] : $link_text;
				} else {
					$oracion['ltext'] = $this->Junk->truncate($data['c_body'], 30);
				}
			break;
		}
		//
		return $oracion;
	}

	/**
	 * @name makeFecha
	 * @access private
	 * @params int
	 * @return string
	*/
	private function makeFecha(int $time = 0): string {
		# VARIABLES LOCALES
		$tiempo = time() - $time; 
		$dias = round($tiempo / 86400);
		return match (true) {
			$dias < 1 => 'today',
			$dias < 2 => 'yesterday',
			$dias <= 7 => 'week',
			$dias <= 30 => 'month',
			default => 'old',
		};
	}
}