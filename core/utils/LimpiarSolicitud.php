<?php 

/**
 * Proyecto de modernización de PHPost
 * 
 * Seguridad básica para limpiar solicitudes HTTP
 * 
 * @author    Miguel92
 * @license   MIT
 * @link      https://github.com/joelmiguelvalente
 * @link      https://www.linkedin.com/in/joelmiguelvalente/
 */

class LimpiarSolicitud {

	private array $get = [];
	
	private array $post = [];

	private array $cookie = [];

	public function __construct() {}

	/**
	 * Ejecuta la limpieza general de la solicitud
	 * Incluye validaciones y sanitización completa
	 */
	public function clear(): void {
		$this->validarGlobals();
		$this->validarReferer();
		$this->validarClaves();
		$this->sanitizarEntradas();
	}

	/**
	 * Ejecuta limpieza puntual sobre superglobales elegidas
	 * Usa htmlspecialchars como refuerzo
	 */
	public function run(array $targets = ['post', 'get', 'cookie']): void {
		foreach ($targets as $t) {
			if (method_exists($this, $t)) $this->$t();
		}
	}

	/**
	 * Validación de acceso a GLOBALS
	 */
	private function validarGlobals(): void {
		if (isset($_REQUEST['GLOBALS']) || isset($_COOKIE['GLOBALS'])) {
			http_response_code(400);
			exit('Solicitud no válida: acceso a GLOBALS');
		}
	}

	/**
	 * Previene referers cruzados (CSRF básico)
	 */
	private function validarReferer(): void {
		$referer = $_SERVER['HTTP_REFERER'] ?? '';
		$host = $_SERVER['HTTP_HOST'] ?? '';
		$refererHost = parse_url($referer, PHP_URL_HOST);
		if (!empty($referer) && $refererHost && $refererHost !== $host && $_SERVER['REQUEST_METHOD'] === 'POST') {
			http_response_code(403);
			exit('Solicitud no autorizada.');
		}
	}

	/**
	 * Valida que las claves de entrada no sean numéricas ni excesivamente largas
	 */
	private function validarClaves(): void {
		$fuentes = [$_GET, $_POST, $_COOKIE, $_FILES];
		foreach ($fuentes as $fuente) {
			foreach ($fuente as $key => $_) {
				if (ctype_digit((string)$key)) {
					http_response_code(400);
					exit('Claves numéricas no permitidas.');
				}
				if (strlen($key) > 64) {
					http_response_code(400);
					exit('Claves demasiado largas.');
				}
			}
		}
	}

	/**
	 * Sanitiza entradas usando filtros seguros
	 */
	private function sanitizarEntradas(): void {
		$this->get    = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS)    ?? [];
		$this->post   = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS)   ?? [];
		$this->cookie = filter_input_array(INPUT_COOKIE, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? [];
		// Reemplazo seguro en superglobales
		$_GET    = $this->get;
		$_POST   = $this->post;
		$_COOKIE = $this->cookie;
		// Opcional: eliminar REQUEST para evitar ambigüedad
		unset($_REQUEST);
	}

	private function setString(mixed $value): mixed {
		if (is_array($value)) {
			return array_map([$this, 'setString'], $value);
		}
		return is_string($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
	}

	/**
	 * Sanitiza manualmente $_POST
	 */
	private function post(): void {
		foreach ($_POST as $key => $value) $_POST[$key] = $this->setString($value);
	}

	/**
	 * Sanitiza manualmente $_GET
	 */
	private function get(): void {
		foreach ($_GET as $key => $value) $_GET[$key] = $this->setString($value);
	}

	/**
	 * Sanitiza manualmente $_COOKIE
	 */
	private function cookie(): void {
		foreach ($_COOKIE as $key => $value) $_COOKIE[$key] = $this->setString($value);
	}
}