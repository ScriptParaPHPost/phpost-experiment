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

class tsTops {

	protected tsCore $tsCore;

	protected tsUser $tsUser;

	protected Junk $Junk;

	private $filter = ['hoy' => 1, 'ayer' => 2, 'semana' => 3, 'mes' => 4, 'historico' => 5];

	public function __construct(Tops $deps) {
		$this->tsCore = $deps->tsCore;
		$this->tsUser = $deps->tsUser;
		$this->Junk = $deps->Junk;
	}

	private function getHomeTops(string|array $array, $function = '') {
		if(is_array($array)) {
			foreach ($array as $tiempo => $settime) {
				$data[$tiempo] = call_user_func(array($this, $function), $this->setTime($settime));
			}
		} else {
			$data = call_user_func(array($this, $function), $this->setTime($array));
		}
		return $data;
	}

	/*
		getHomeTopPosts()
		: TOP DE POST semana, histórico
	*/
	public function getHomeTopPosts(string $type, bool $encode = true) {
		array_shift($this->filter);
		$data = $this->getHomeTops($this->filter, 'getHomeTopPostsQuery');
		return $encode ? json_encode($data[$type]) : $data[$type];
	}

	/*
		getHomeTopUsers()
		: TOP DE USUARIOS semana, histórico
	*/
	public function getHomeTopUsers(string $type, bool $encode = true) {
		array_shift($this->filter);
		$data = $this->getHomeTops($this->filter, 'getHomeTopUsersQuery');
		return $encode ? json_encode($data[$type]) : $data[$type];
	}

	/*
		getTopUsers()
	*/
	public function getTopUsers(int $date = 0, int $category = 0): array {
		//
		$data = $this->setTime($date);
		$category = empty($category) ? '' : 'AND post_category = '.$category;
		$between = "BETWEEN {$data['start']} AND {$data['end']}";
		// PUNTOS
		$query = db_exec([__FILE__, __LINE__], 'query', "SELECT SUM(p.post_puntos) AS total, u.user_id, u.user_name FROM p_posts AS p LEFT JOIN u_miembros AS u ON p.post_user = u.user_id WHERE p.post_status = 0 AND p.post_date $between $category GROUP BY p.post_user ORDER BY total DESC LIMIT 10");
		$array['puntos'] = result_array($query);
		
		// SEGUIDORES
		$query = db_exec([__FILE__, __LINE__], 'query', "SELECT COUNT(f.follow_id) AS total, u.user_id, u.user_name FROM u_follows AS f LEFT JOIN u_miembros AS u ON f.f_id = u.user_id WHERE f.f_type = 1 AND f.f_date $between GROUP BY f.f_id ORDER BY total DESC LIMIT 10");
		$array['seguidores'] = result_array($query);
		
		// MEDALLAS
		$query = db_exec([__FILE__, __LINE__], 'query', "SELECT COUNT(m.medal_for) AS total, u.user_id, u.user_name, wm.medal_id FROM w_medallas_assign AS m LEFT JOIN u_miembros AS u ON m.medal_for = u.user_id LEFT JOIN w_medallas AS wm ON wm.medal_id = m.medal_id WHERE wm.m_type = 1 AND m.medal_date $between GROUP BY m.medal_for ORDER BY total DESC LIMIT 10");
		$array['medallas'] = result_array($query);
		
		//
		return $array;
	}

	/*
		getTopPosts()
	*/
	public function getTopPosts(int $date = 0, int $category = 0): array {
		$items = [ 'puntos', 'seguidores', 'comments', 'favoritos' ];
		foreach($items as $item) {
			$data[$item] = $this->getTopPostsVars($date, $category, $item);
		}
		return $data;
	}
	/*
		setTopPostsVars($text, $type)
	*/
	private function getTopPostsVars(int $date = 0, int $category = 0, string $type = '') {
		$data = $this->setTime($date);
		$data['category'] = empty($category) ? '' : 'AND c.cid = '.$category;
		$data['type'] = 'p.post_'.$type;
		return $this->getTopPostsQuery($data);
	}

	/*
		getTopPostsQuery($data)
	*/
	public function getTopPostsQuery(array $data = []): array {
		$datos = result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT p.post_id, p.post_category, {$data['type']}, p.post_puntos, p.post_title, c.c_seo, c.c_img FROM p_posts AS p LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE p.post_status = 0 AND p.post_date BETWEEN {$data['start']} AND {$data['end']} {$data['category']} ORDER BY {$data['type']} DESC LIMIT 10"));
		return $datos;
	}

	/*
		getHomeTopPostsQuery($data)
	*/
	public function getHomeTopPostsQuery(array $data = []): array {
		$data = result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT p.post_id, p.post_category, p.post_title, p.post_puntos, c.c_seo FROM p_posts AS p LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE p.post_status = 0 AND p.post_date BETWEEN {$data['start']} AND {$data['end']} ORDER BY p.post_puntos DESC LIMIT 10"));
		foreach($data as $pid => $post) {
			$data[$pid]['title'] = $post['post_title'];
			$data[$pid]['url'] = $this->Junk->UrlBuilder($post);
			$data[$pid]['puntos'] = $post['post_puntos'];
		}
		return $data;
	}

	/*
		getHomeTopUsersQuery($date)
	*/
	public function getHomeTopUsersQuery(array $data = []): array {
		$data = result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT SUM(p.post_puntos) AS total, u.user_id, u.user_name FROM p_posts AS p LEFT JOIN u_miembros AS u ON p.post_user = u.user_id WHERE p.post_status = 0 AND p.post_date BETWEEN {$data['start']} AND {$data['end']} GROUP BY p.post_user ORDER BY total DESC LIMIT 10"));
		foreach($data as $uid => $user) {
			$data[$uid]['title'] = $user['user_name'];
			$data[$uid]['url'] = $this->Junk->UrlBuilder($user, 'perfil');
			$data[$uid]['puntos'] = $user['total'];
		}
		return $data;
	}
	
	/*
		setTime($fecha)
	*/
	public function setTime($fecha){
		// Obtiene la fecha actual en formato UNIX
		$tiempo = strtotime('now');
		switch($fecha){
		  // HOY
		  case 1: 
				$data['start'] = strtotime('midnight today');
				$data['end'] = strtotime('tomorrow -1 second');
			break;
		  // AYER
		  case 2: 
				$data['start'] = strtotime('midnight -1 day');
				$data['end'] = strtotime('midnight');
			break;
			// SEMANA
			case 3: 
				$data['start'] = strtotime('-1 week');
				$data['end'] = strtotime('tomorrow -1 second');
			break;
			// MES
			case 4: 
				$data['start'] = strtotime('first day of this month', $tiempo);
				$data['end'] = strtotime('tomorrow', $tiempo) - 1;
			break;
			// TODO EL TIEMPO
			case 5: 
			default: 
				$data['start'] = 0;
				$data['end'] = $tiempo;
			break;
		}
		return $data;
	}
}