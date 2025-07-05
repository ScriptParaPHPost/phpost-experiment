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

if (!defined('PHPOST_CORE_LOADED')) 
	exit('Acceso denegado: ¡No puedes acceder este script directamente!');

class tsCore {
	 
	public array $settings = [];

	/**
	 * Constructor de la clase principal del sistema.
	 * 
	 * Carga la configuración inicial.
	 */
	public function __construct() {
		// Cargar configuraciones principales
		$this->settings = $this->getSettings();
		// Cargar contadores de elementos pendientes de moderación
		$this->settings['novemods'] = $this->getNovemods();
	}

	/**
	 * Recupera la configuración completa del sistema desde varias tablas de configuración.
	 *
	 * Utiliza subconsultas con `CROSS JOIN` para combinar todas las configuraciones 
	 * en una sola fila, siempre que `tscript_id = 1` esté presente en todas.
	 *
	 * @return array Retorna un array asociativo con las configuraciones 
	 * combinadas de las distintas tablas.
	 *
	 * @throws RuntimeException Si ocurre un error al ejecutar la consulta.
	 */
	public function getSettings(): array {
		$sql = "SELECT g.titulo, g.slogan, g.url, g.email, g.tema_id, u.c_last_active, u.c_reg_active, u.c_reg_activate, u.c_reg_rango, u.c_allow_portal, u.c_fotos_private, u.c_see_mod, l.c_max_posts, l.c_max_com, l.c_max_nots, l.c_max_acts, l.c_newr_type, m.offline, m.offline_message, m.version, m.version_code
		FROM (SELECT * FROM w_config_general WHERE tscript_id = 1) AS g
		CROSS JOIN (SELECT * FROM w_config_users WHERE tscript_id = 1) AS u
		CROSS JOIN (SELECT * FROM w_config_limits WHERE tscript_id = 1) AS l
		CROSS JOIN (SELECT * FROM w_config_misc WHERE tscript_id = 1) AS m";
		$config = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', $sql));
		if (!$config) {
			throw new RuntimeException("No se pudo recuperar la configuración del sistema.");
		}
		return $config;
	}

	/**
	 * Genera y retorna las rutas base del sistema: dominio, tema, archivos estáticos y directorio de archivos subidos.
	 *
	 * @return array Arreglo asociativo con claves.
	 */
	public function setRoutes(string $basePath = '', string $subPath = ''): array|string {
		$baseUrl = $this->settings['url'];
		$domain = str_replace(['https://', 'http://'], '', $baseUrl);
		$themePath = $baseUrl . '/views/' . $this->getTema('t_path');
		// Rutas base
		$routes = [
			'url'    => $baseUrl,
			'domain' => $domain,
			'tema'   => $themePath,
			'files'  => $baseUrl . '/files/',
		];
		// Agrega rutas para recursos estáticos
		foreach (['images', 'css', 'js'] as $folder) {
			$routes[$folder] = "$themePath/$folder";
		}
		return (empty($basePath) && empty($subPath)) ? $routes : $routes[$basePath].$subPath;
	}

	/**
	 * Obtiene estadísticas de moderación: reportes, revisiones y elementos en papelera.
	 *
	 * @return array Retorna un array.
	 */
	public function getNovemods(): array {
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT 
		(SELECT COUNT(post_id) FROM p_posts WHERE post_status = '3') AS revposts,
		(SELECT COUNT(cid) FROM p_comentarios WHERE c_status = '1') AS revcomentarios,
		(SELECT COUNT(DISTINCT obj_id) FROM w_denuncias WHERE d_type = '1') AS repposts,
		(SELECT COUNT(DISTINCT obj_id) FROM w_denuncias WHERE d_type = '2') AS repmps,
		(SELECT COUNT(DISTINCT obj_id) FROM w_denuncias WHERE d_type = '3') AS repusers,
		(SELECT COUNT(DISTINCT obj_id) FROM w_denuncias WHERE d_type = '4') AS repfotos,
		(SELECT COUNT(susp_id) FROM u_suspension) AS suspusers,
		(SELECT COUNT(post_id) FROM p_posts WHERE post_status = '2') AS pospelera,
		(SELECT COUNT(foto_id) FROM f_fotos WHERE f_status = '2') AS fospelera"));
		// Calcular total de elementos que requieren atención de moderación
		$data['total'] = array_sum([$data['revposts'],$data['revcomentarios'],$data['repposts'],$data['repmps'],$data['repusers'],$data['repfotos']]);

		return $data;
	}

	/**
	 * Obtiene todas las categorías de publicaciones disponibles en el sistema.
	 * 
	 * @return array
	 */
	public function getCategorias(): array {
		$sql = "SELECT cid, c_orden, c_nombre, c_seo, c_img FROM p_categorias ORDER BY c_orden";
		return result_array(db_exec([__FILE__, __LINE__], 'query', $sql));
	}

	/**
	 * Obtiene información del tema actual en uso.
	 *
	 * @param string $col Nombre de la columna específica a devolver (t_name, t_url, t_path, t_copy).
	 * Si se omite, se devuelve el array completo.
	 *
	 * @return string|array Devuelve un string si se solicita una columna específica, 
	 * o un array con todos los datos del tema.
	 */
	public function getTema(string $col = ''): string|array {
		$temaID = (int) $this->settings['tema_id'];
		$sql = "SELECT t_name, t_url, t_path, t_copy FROM w_temas WHERE tid = $temaID LIMIT 1";
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', $sql));
		// Si se solicitó una columna específica, la retornamos (o vacía si no existe)
		return $col === '' ? $data : ($data[$col] ?? '');
	}

	/**
	 * Obtenemos todas las noticias del sitio
	 * 
	 * @return array
	 */
	public function getNews() {
		// Cargar noticias solo en secciones específicas
		$seccion = $_GET['do'] ?? '';
		if (!in_array($seccion, ['portal', 'posts'])) return false;
		$news = result_array(db_exec([__FILE__, __LINE__], 'query', 'SELECT not_body FROM w_noticias WHERE not_active = 1 ORDER by RAND()'));
		foreach($news as $nid => $new) {
			$news[$nid]['not_body'] = $this->parseBBCode($new['not_body'], 'news');
		}
		return $news;
	}
	
	// FUNCIÓN CONCRETA PARA CENSURAR
	function parseBadWords($c, $s = FALSE) {
		$q = result_array(db_exec([__FILE__, __LINE__], 'query', 'SELECT word, swop, method, type FROM w_badwords '.($s == true ? '' : ' WHERE type = \'0\'')));
		foreach($q AS $badword) {
			$c = str_ireplace((empty($badword['method']) ? $badword['word'] : $badword['word'].' '),($badword['type'] == 1 ? '<img title="'.$badword['word'].'" src="'.$badword['swop'].'" />' : $badword['swop'].' '),$c);
		}
		return $c;
	}        
	
	/*
		setLevel($tsLevel) :: ESTABLECE EL NIVEL DE LA PAGINA | MIEMBROS o VISITANTES
	*/
	public function setLevel($tsLevel, $msg = false){
		global $tsUser;
		// Los mensajes
		$setMessages = [
			1 => 'Esta p&aacute;gina solo es vista por los visitantes.',
			2 => 'Para poder ver esta p&aacute;gina debes iniciar sesi&oacute;n.',
			3 => 'Estas en un &aacute;rea restringida solo para moderadores.',
			4 => 'Estas intentando algo no permitido.'
		];
		// Definimos los accesos!
		$conditions = [
			0 => true, // CUALQUIERA
			1 => $tsUser->is_member === 0, // SOLO VISITANTES
			2 => $tsUser->is_member === 1, // SOLO MIEMBROS
			3 => $tsUser->is_admod || (!empty($tsUser->permisos) && isset($tsUser->permisos['moacp']) && $tsUser->permisos['moacp']), // SOLO MODERADORES
			4 => $tsUser->is_admod === 1 // SOLO ADMIN
		];
		$tsLevel = $tsLevel ?? 0;
		
		if (isset($conditions[$tsLevel]) && $conditions[$tsLevel]) return true;
		// Manejo de mensajes de error
		$message = $setMessages[$tsLevel];
		return ($message) ? $message : ['titulo' => 'Error', 'mensaje' => $message ?? 'Error desconocido.'];
		// Redireccionamiento
		$redirects = ((int)$tsLevel === 1) ? '/' : '/login/?r='.$this->currentUrl();
		$this->redirectTo($redirects);
	}

	/*
		redirect($tsDir)
	*/
	function redirectTo($tsDir){
		header("Location: " . urldecode($tsDir));
		exit();
	}

	# Obtenemos el dominio
	public function getDomain() {
		$host = parse_url($this->withoutSSL(), PHP_URL_HOST); // Obtiene solo el dominio
		$parts = explode('.', $host);
		// Si es localhost o IP (desarrollo)
		if ($host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)) {
			return $host;
		}
		$count = count($parts);
		if ($count >= 2) {
			return $parts[$count - 2] . '.' . $parts[$count - 1]; // ejemplo.com
		}
		// Dominio no válido
		return $host;
	}

	# Obtenemos url codificada
	public function currentUrl(){
		$current_url = $this->https_on() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		return urlencode($current_url);
	}

	/**
	 * Establece el limite de paginas y el inicio para la paginacion.
	 *
	 * @param int $tsLimit El limite de resultados por pagina.
	 * @param bool $start Indica si se debe establecer el inicio de la paginacion.
	 * @param int $tsMax El numero maximo de resultados permitidos.
	 * @return string El inicio y el limite de resultados como una cadena.
	*/
	public function setPageLimit($tsLimit, $start = false, $tsMax = 0){
		// Inicializar el inicio de la paginacion
		$tsStart = 0;
		// Establecer el inicio de la paginacion si es necesario
		if ($start !== false) {
			$tsStart = isset($_GET['s']) ? (int) $_GET['s'] : 0;
			// Establecer el inicio en 0 si se excede el limite maximo
			if ($this->setMaximos($tsLimit, $tsMax)) {
				$tsStart = 0;
			}
		} else {
			// Calcular el inicio basado en el numero de pagina
			$pageNumber = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
			$tsStart = ($pageNumber - 1) * $tsLimit;
		}
		// Retornar el inicio y el limite de resultados
		return "$tsStart,$tsLimit";
	}
	
	/**
	 * Verifica si se excede el limite maximo de paginas.
	 *
	 * @param int $tsLimit El limite de resultados por pagina.
	 * @param int $tsMax El numero maximo de resultados permitidos.
	 * @return bool True si se excede el limite maximo, false en caso contrario.
	*/
	public function setMaximos(int $tsLimit = 0, int $tsMax = 0) {
		// MAXIMOS || PARA NO EXEDER EL NUMERO DE PAGINAS
		$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
		$ban1 = ($page * $tsLimit);
		if($tsMax < $ban1){
			$ban2 = $ban1 - $tsLimit;
			if($tsMax < $ban2) return true;
		} 
		//
		return false;
	}

	/**
	 * Genera informaci�n sobre la paginaci�n de un conjunto de resultados.
	 *
	 * @param int $tsTotal El n�mero total de resultados.
	 * @param int $tsLimit El l�mite de resultados por p�gina.
	 * @return array La informaci�n de paginaci�n.
	 */
	public function getPages(int $tsTotal = 0, int $tsLimit = 0) {
		// Verificar si el l�mite es v�lido
		if ($tsLimit <= 0) {
			return []; // Devolver un array vac�o si el l�mite es cero o negativo
		}
		// Calcular el n�mero total de p�ginas
		$tsPages = ceil($tsTotal / $tsLimit);
		// Obtener el n�mero de p�gina actual
		$tsPage = isset($_GET['page']) ? max(1, min($_GET['page'], $tsPages)) : 1;
		// Verificar si el n�mero de p�gina actual excede el total de p�ginas
		if ($tsPage > $tsPages) {
			$tsPage = $tsPages;
		}
		// Construir el array de informaci�n de paginaci�n
		$pages = [
			'current' => $tsPage,
			'pages' => $tsPages,
			'section' => $tsPages + 1,
			'prev' => max(1, $tsPage - 1),
			'next' => min($tsPages, $tsPage + 1),
			'max' => $this->setMaximos($tsLimit, $tsTotal)
		];
		// Retornar la informaci�n de paginaci�n
		return $pages;
	}

	/*
		getPagination($total, $per_page)
	*/
	public function getPagination($total, $per_page = 10){
		// PAGINA ACTUAL
		$page = empty($_GET['page']) ? 1 : (int) $_GET['page'];
		// NUMERO DE PAGINAS
		$num_pages = ceil($total / $per_page);
		// ANTERIOR
		$prev = $page - 1;
		$pages['prev'] = ($page > 0) ? $prev : 0;
		// SIGUIENTE 
		$next = $page + 1;
		$pages['next'] = ($next <= $num_pages) ? $next : 0;
		// LIMITE DB
		$pages['limit'] = (($page - 1) * $per_page).','.$per_page; 
		// TOTAL
		$pages['total'] = $total;
		//
		return $pages;
	}

	/**/
	public function pageIndex($base_url, int $max_value, int $num_per_page, bool $flexible_start = false) {
		// Remove the 's' parameter from the base URL
		$base_url = $this->settings['url'] . $base_url;
		$base_url = preg_replace('/[?&]s=\d*/', '', $base_url);
		// Ensure $start is a non-negative integer and a multiple of $num_per_page
		$start = max(1, (isset($_GET['s']) ? (int)$_GET['s'] : 0));
		$start -= $start % $num_per_page;
		$morepages = '<div class="page-item off"><span class="page-numbers">...</span></div>';

		// Initialize the page index string
		$pageindex = '';
		$pageindex .= '<nav class="pagination">';
		// Generate the link format based on whether flexible_start is enabled or not
		$flexstart = $base_url . ($flexible_start ? '' : '&s=%d');
		$base_link = "<div class=\"page-item\"><a class=\"page-numbers\" href=\"$flexstart\">%s</a></div> ";
		
		// Calculate the number of contiguous page links to show
		$PageContiguous = 2;
		// Helper function to generate page links
		$generatePageLink = function ($pageNumber) use ($base_link, $num_per_page) {
			return sprintf($base_link, $pageNumber * $num_per_page, $pageNumber + 1);
		};
		// Add the link to the first page if necessary
		if ($start > $num_per_page * $PageContiguous) {
			  $pageindex .= $generatePageLink(0) . ' ';
		}
		// Add '...' before the first page link if necessary
		if ($start > $num_per_page * ($PageContiguous + 1)) {
			  $pageindex .= $morepages;
		}
		// Add page links before the current page
		for ($i = $PageContiguous; $i >= 1; $i--) {
			  $pageNumber = $start / $num_per_page - $i;
			  if ($pageNumber >= 0) {
					$pageindex .= $generatePageLink($pageNumber);
			  }
		}
		// Add the link to the current page
		$pageindex .= '<div class="page-item"><span aria-current="page" class="page-numbers current">' . ($start / $num_per_page + 1) . '</span></div> ';
		// Add page links after the current page
		for ($i = 1; $i <= $PageContiguous; $i++) {
			  $pageNumber = $start / $num_per_page + $i;
			  // Ensure the link is within the valid page range
			  if ($pageNumber * $num_per_page < $max_value) {
					$pageindex .= $generatePageLink($pageNumber);
			  }
		}
		// Add '...' near the end if necessary
		if ($start + $num_per_page * ($PageContiguous + 1) < $max_value - $num_per_page) {
			  $pageindex .= $morepages;
		}
		// Add the link to the last page if necessary
		if ($start + $num_per_page * $PageContiguous < $max_value - $num_per_page) {
			  $pageNumber = (int) (($max_value - 1) / $num_per_page);
			  $pageindex .= $generatePageLink($pageNumber);
		}
		$pageindex .= '</nav>';
		return $pageindex;
	}

	/**
	 * Sanitiza un valor para seguridad en base de datos y XSS.
	 *
	 * @param string $var El valor a limpiar.
	 * @param bool $xss Si se desea aplicar protección contra XSS.
	 * @return string El valor limpio.
	 */
	public function setSecure(string $var, bool $xss = false): string {
		// Primero escapamos para la base de datos
		$var = db_exec('real_escape_string', $var);

		// Luego opcionalmente limpiamos para HTML (evita XSS)
		if ($xss) {
			$var = htmlspecialchars($var, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		}

		return $var;
	}

	# Evitamos que realice muchas tareas en poco tiempo
	public function antiFlood(bool $print = true, string $type = 'post', string $msg = '') {
		global $tsUser;
		//
		$now = time();
		$msg = empty($msg) ? 'No puedes realizar tantas acciones en tan poco tiempo.' : $msg;
		//
		$_SESSION['flood'][$type] = (!isset($_SESSION['flood'][$type])) ? '' : 3;
		$limit = $tsUser->permisos['goaf'];
		$resta = $now - $_SESSION['flood'][$type];
		if($resta < $limit) {
			$msg = '0: '.$msg.' Int&eacute;ntalo en '.($limit - $resta).' segundos.';
			// TERMINAR O RETORNAR VALOR
			if($print) die($msg);
			else return $msg;
		} else {
			// ANTIFLOOD
			$_SESSION['flood'][$type] = (empty($_SESSION['flood'][$type])) ? time() : $now;
			// TODO BIEN
			return true;
		}
	}
	
	/**
	 * Genera una versión SEO amigable de una cadena para usar en URLs (slug).
	 *
	 * @param string $string El texto original.
	 * @param int|null $max Longitud máxima (opcional, no se usa por ahora).
	 * @return string Texto optimizado para URL (slug).
	 */
	public function setSEO(string $string, ?int $max = null): string {
		// Normalizar caracteres con acentos, eñes, etc.
		$string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
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

	/*
		parseBBCode($bbcode)
	*/
	public function parseBBCode($bbcode, $type = 'normal') {
		// Class BBCode
		include_once TS_EXTRA . 'bbcode.inc.php';
		// Class BBCode
		$parser = new BBCode();
		// Seleccionar texto
		$parser->setText($bbcode);
		//
		$buttons = [
			'normal' => ['url', 'code', 'quote', 'font', 'size', 'color', 'img', 'b', 'i', 'u', 's', 'align', 'spoiler', 'video', 'hr', 'sub', 'sup', 'table', 'td', 'tr', 'ul', 'li', 'ol', 'notice', 'info', 'warning', 'error', 'success'],
		  'firma' => ['url', 'font', 'size', 'color', 'img', 'b', 'i', 'u', 's', 'align', 'spoiler'],
		  'news' => ['url', 'b', 'i', 'u', 's']
		];
		// Determinar si el tipo es 'normal' o 'smiles', en cuyo caso usar� los botones de 'normal'
		$allowed_buttons = ($type === 'normal' || $type === 'smiles') ? $buttons['normal'] : $buttons[$type];
		$parser->setRestriction($allowed_buttons);
		// Parsear menciones si el tipo es 'normal' o 'smiles'
		if ($type === 'normal' || $type === 'smiles') {
			$parser->parseMentions();
		}
		// Parsear smiles si el tipo es 'normal', 'smiles' o 'news'
		$parser->parseSmiles();
		// Retornar resultado en HTML
		return $parser->getAsHtml();
	}

	/**
	 * Reemplaza menciones del tipo @usuario por enlaces HTML.
	 *
	 * @deprecated Usa $parser->parseMentions() en su lugar. Esta función se mantiene solo por compatibilidad.
	 *
	 * @param string $html Contenido HTML con menciones tipo @usuario
	 * @return string HTML con las menciones convertidas a enlaces
	 */
	public function setMenciones(string $html): string {
		global $tsUser;
		// Buscar menciones con @ seguidas de 4 a 16 caracteres válidos
		if (preg_match_all('/\B@([a-zA-Z0-9_-]{4,16})\b/', $html, $matches)) {
			$usuarios = array_unique($matches[1]); // Evitar usuarios repetidos
			foreach ($usuarios as $user) {
				$uid = $tsUser->getUserID($user);
				if ($uid) {
					$profileUrl = $this->settings['url'] . '/@' . urlencode($user);
					// Usamos una expresión regular segura para reemplazar solo menciones exactas
					$user = htmlspecialchars($user);
					$html = preg_replace(
						'/@' . preg_quote($user, '/') . '\b/',
						'@<a href="' . $profileUrl . '" title="' . $user . '">' . $user . '</a>',
						$html
					);
				}
			}
		}
		return $html;
	}

	/**
	 * setHace()
	 * if ternario
	*/
	public function setHace(int $fecha = 0, $show = false){
		# Creamos
		$tiempo = time() - $fecha;
		if($fecha <= 0) return "Nunca";
		// Declaraci�n de unidades de tiempo, aunque es un aproximado
		// Ya que existe a�os bisiestos 366 d�as
		$unidades = [
		  31536000 => ["a&ntilde;o", "a&ntilde;os"],
		  2678400 => ["mes", "meses"],
		  604800 => ["semana", "semanas"],
		  86400 => ["d&iacute;a", "d&iacute;as"],
		  3600 => ["hora", "horas"],
		  60 => ["minuto", "minutos"],
		];
		foreach($unidades as $segundos => $nombre){
			$round = round($tiempo / $segundos);
			$s = ($segundos === 2678400) ? 'es' : 's';
			if($tiempo <= 60) $hace = "instantes";
			else {
				if($round > 0) {
					$hace = "{$round} {$nombre[($round > 1 ? 1 : 0)]}";
					break;
				}
			}
		}
		// Si se ha establecido la opci�n $show, se agrega 'Hace' al resultado
		return ($show ? "Hace " : "") . $hace;
	}

	/*
		getUrlContent($tsUrl)
	*/
	public function getUrlContent(string $tsUrl): ?string {
		// USAMOS CURL O FILE
		if(function_exists('curl_init')){
			//Abrir conexion  
			$ch = curl_init();  
			curl_setopt($ch, CURLOPT_USERAGENT, 		$_SERVER['HTTP_USER_AGENT']);
			curl_setopt($ch, CURLOPT_URL,		 			$tsUrl);
			curl_setopt($ch, CURLOPT_TIMEOUT, 		  	60);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 	1);
			$result = curl_exec($ch);
			curl_close($ch); 
		} else $result = @file_get_contents($tsUrl);
		return $result;
	}

	/*
		 getIP
	*/
	function getIP(){
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) $ip = getenv('HTTP_CLIENT_IP');	
		elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) $ip = getenv('HTTP_X_FORWARDED_FOR');
		elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) $ip = getenv('REMOTE_ADDR');
		elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) $ip = $_SERVER['REMOTE_ADDR'];
		else $ip = 'unknown';
		return $this->setSecure($ip);
	}

	/**
	 * Genera una cadena SQL para actualizar valores en la base de datos
	 *
	 * @param array $array Array asociativo con los campos y valores a actualizar
	 * @param string $prefix Prefijo para los campos
	 * @return string Cadena SQL con los campos actualizados
	*/
	public function getIUP(array $array = [], string $prefix = ''): string {
		$sets = [];
		foreach ($array as $field => $value) {
			$sets[] = "$prefix$field = " . (is_numeric($value) ? (int)$value : "'{$this->setSecure($value)}'");
		}
		return implode(', ', $sets);
	}
	
}