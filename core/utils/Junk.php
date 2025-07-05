<?php

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

class Junk {

	public array $set = [];

	public string $avatar = '/files/avatar/avatar.webp';

	private array $redes = [
		'facebook' => 'Facebook', 
		'twitter' => 'Twitter', 
		'instagram' => 'Instagram',
		'youtube' => 'Youtube',
		'twitch' => 'Twitch'
	];

	public function __construct() {
		$this->set['social'] = $this->redes;
		$this->set['IP'] = $this->getClientIP();
		$this->set['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
		$this->set['referer'] = $_SERVER['HTTP_REFERER'] ?? 'direct';
	}

	/**
	 * Verifica si un usuario bloqueó a otro.
	*/
	public function UserIsBlockeUser(int $fromUserId = 0, int $toUserId = 0): bool {
		$exists = db_exec('num_rows', db_exec([__FILE__, __LINE__], 'query', "SELECT b_user, b_auser FROM `u_bloqueos` WHERE b_user = $fromUserId AND b_auser = $toUserId LIMIT 1"));
		return ($exists > 0);
	}

	/**
	 * Obtener el public key | secret key
	 */
	public function setKeys(string $type = 'public'):string {
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT pkey, skey FROM w_config_misc WHERE tscript_id = 1"));
		return $data[($type === 'public' ? 'pkey' : 'skey')];
	}

	/**
	 * Obtener el theme actual
	 */
	public function setTheme():string {
		$tid = (int)db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT tema_id FROM w_config_general WHERE tscript_id = 1"))['tema_id'];
		$tema = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT t_path FROM w_temas WHERE tid = $tid"));
		return $tema['t_path'];
	}

	/**
	 * Retorna la IP pública del cliente, priorizando headers seguros.
	 */
	private function getClientIP(): string {
		$headers = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
		foreach ($headers as $header) {
			if (!empty($_SERVER[$header]) && $this->isValidIP($_SERVER[$header])) {
				return $_SERVER[$header];
			}
		}
		return 'unknown';
	}

	/**
	 * Verifica si una IP es válida (IPv4 o IPv6).
	 */
	private function isValidIP(string $ip): bool {
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false;
	}

	/**
	 * Permite agregar valores al array `set` desde fuera de la clase.
	*/
	public function setValue(string $key, mixed $value): void {
		$this->set[$key] = $value;
	}

	/**
	 * Devuelve el valor de una clave en `set`, o null si no existe.
	 */
	public function getValue(string $key): mixed {
		return $this->set[$key] ?? null;
	}

	/**
	 * Devuelve información de tiempo actual
	 *
	 * @param string $formato    Formato de salida ('timestamp', 'string', 'diff')
	 * @param int|null $referencia  Timestamp para comparar si se elige 'diff'
	 * @param bool $utc          Si true, devuelve la hora en UTC
	 * @return mixed
	 */
	public function setTime(string $formato = 'timestamp', ?int $referencia = null, bool $utc = false): mixed {
		$now = $utc ? gmdate('U') : time();

		switch ($formato) {
			case 'string':
				return $utc ? gmdate('Y-m-d H:i:s', $now) : date('Y-m-d H:i:s', $now);

			case 'diff':
				if ($referencia === null) return null;
				return $now - $referencia;

			case 'timestamp':
			default:
				return (int) $now;
		}
	}

	public function truncate($text, $limit = 30, $suffix = '...') {
    	$text = trim($text); // Elimina espacios en blanco innecesarios
	   if (strlen($text) <= $limit) return $text;
	   return substr($text, 0, $limit) . $suffix;
	}

	/**
	 * Genera una versión SEO amigable de una cadena para usar en URLs (slug).
	 *
	 * @param string $string El texto original.
	 * @param int|null $max Longitud máxima (opcional, no se usa por ahora).
	 * @return string Texto optimizado para URL (slug).
	 */
	public function setSEO(string $string, ?int $max = null): string {
		// Requiere la extensión intl habilitada
		if (class_exists('Normalizer')) {
			$string = Normalizer::normalize($string, Normalizer::FORM_D);
			$string = preg_replace('/\p{Mn}/u', '', $string); // Quitar marcas diacríticas (tildes)
		}
		// Reemplazar todo lo que no sea alfanumérico por guiones
		$string = preg_replace('/[^a-zA-Z0-9]+/', '-', $string);
		// Eliminar guiones al principio/final, y convertir a minúsculas
		$slug = strtolower(trim($string, '-'));
		// Limitar longitud si se especifica
		if ($max !== null) {
			$slug = substr($slug, 0, $max);
			$slug = rtrim($slug, '-'); // Evita terminar con un guión
		}
		return $slug;
	}

	private function getBaseUrl(): string {
    	$endpoint = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT url FROM w_config_general WHERE tscript_id = 1"));
    	return $endpoint['url'] ?? '/';
	}

	private function buildPostUrl(array $data): string {
	   return "/posts/{$data['c_seo']}/{$data['post_id']}/" . $this->setSEO($data['post_title']) . ".html";
	}

	private function buildProfileUrl(array $data, string $suffix = ''): string {
	   return "/@{$data['user_name']}$suffix";
	}

	private function buildPhotoUrl(array $data): string {
	   return "/fotos/{$data['user_name']}/{$data['foto_id']}/" . $this->setSEO($data['f_title']) . ".html";
	}

	private function buildMedalUrl(array $data): string {
	   return "/views/{$this->setTheme()}/images/medallas/{$data['m_image']}.png";
	}

	public function UrlBuilder(array|string $data = null, string $type = 'post', string $param = ''): string {
	   $base = $this->getBaseUrl();
	   return match ($type) {
	      'post'       => $base . $this->buildPostUrl($data),
	      'perfil'     => $base . $this->buildProfileUrl($data, $param),
	      'publicacion'=> $base . $this->buildProfileUrl($data, "/{$data['pub_id']}"),
	      'foto'       => $base . $this->buildPhotoUrl($data),
	      'categoria'	 => $base . '/posts/' . $data,
	      'medalla'	 => $base . $this->buildMedalUrl($data),
	      default      => $base . '/',
	   };
	}

	public function postArrayContent(&$data, Cover $Cover, string $type = 'post') {
		foreach($data as $pid => $post) {
			if($type === 'post') $data[$pid]['post_portada'] 	= $Cover->get((int)$post['post_id'], $post['post_portada']);
			$data[$pid]['post_categoria'] = $this->UrlBuilder($post['c_seo'], 'categoria');
			$data[$pid]['post_url'] 		= $this->UrlBuilder($post);
			$data[$pid]['post_user'] 		= $this->UrlBuilder($post, 'perfil');
		}
	}

}