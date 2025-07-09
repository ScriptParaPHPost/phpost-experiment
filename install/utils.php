<?php

declare(strict_types=1);

/**
 * PHPost 2025
 *
 * Clase Utils
 *
 * Contiene funciones auxiliares reutilizables para el proyecto:
 * - Generación de URLs seguras
 * - Slugs amigables
 * - Envío de emails desde plantillas HTML
 *
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
 */

class Utils {

	/**
	 * Devuelve el esquema de protocolo según si el sitio usa HTTPS o no.
	 *
	 * @param bool $withoutslashes Si es true, incluye "http://" o "https://".
	 *                              Si es false, solo devuelve "http" o "https".
	 * @return string El esquema con o sin slashes, según corresponda.
	 */
	private function secure_url(bool $withoutslashes = true): string {
		$ssl = 'http';
		if (
			(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
			(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
		) {
			$ssl .= 's';
		}
		return $withoutslashes ? $ssl . '://' : $ssl;
	}

	/**
	 * Genera la URL base del sitio con o sin barras al final.
	 *
	 * @param string $page            Ruta adicional a concatenar al final.
	 * @param bool   $withoutSlashes  Si es true, elimina la barra final.
	 * @return string                 URL construida con protocolo, host y base.
	 */
	public function getUrl(string $page = '', bool $withoutSlashes = true): string {
		$scheme = $this->secure_url($withoutSlashes); // http:// o https://
		$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
		// Directorio base del proyecto (adaptado para servidores Windows o UNIX)
		$uri = str_replace('\\', '/', dirname(dirname($_SERVER['REQUEST_URI'])));
		// Elimina barra final si no se desea
		if ($withoutSlashes) {
			$uri = rtrim($uri, '/');
		}
		return $scheme . $host . $uri . $page;
	}

	/**
	 * Convierte una cadena en un slug amigable para URLs.
	 *
	 * Ejemplo:
	 *   "¡Hola mundo PHP!" → "hola-mundo-php"
	 *
	 * @param string $string     Texto de entrada.
	 * @param string $separator  Separador usado en lugar de espacios.
	 * @return string            Cadena limpia, en minúsculas y separada.
	 */
	public function slugly(string $string = '', string $separator = '-'): string {
		$string = htmlentities($string, ENT_QUOTES, 'UTF-8');
		// Reemplaza caracteres especiales HTML como á, ü, ñ, etc.
		$string = preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i','$1',$string);
		$string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
		// Reemplaza todo lo que no sea letra o número por el separador
		$string = preg_replace('~[^0-9a-z]+~i', $separator, $string);
		// Elimina separadores duplicados al inicio/final
		return strtolower(trim($string, $separator));
	}

	/**
	 * Envía un correo electrónico en formato HTML usando una plantilla.
	 *
	 * Reemplaza todas las variables {{nombre}} dentro del HTML con los datos proporcionados.
	 *
	 * @param array  $data       Todo lo necesario
	 * @return bool              True si el correo se envió correctamente.
	 * @throws RuntimeException  Si la plantilla no existe.
	 */
	public function enviar(array $data): bool {
		$rutaPlantilla = __DIR__ . '/../core/templates/' . $data['plantilla'] . '.html';

		if (!file_exists($rutaPlantilla)) {
			throw new RuntimeException("La plantilla '$plantilla' no existe.");
		}

		$contenido = file_get_contents($rutaPlantilla);

		// Reemplazar {{clave}} por valor correspondiente
		foreach ($data['placeholder'] as $clave => $valor) {
			$contenido = str_replace('{{' . $clave . '}}', $valor, $contenido);
		}

		// Cabeceras para enviar correo en formato HTML
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\r\n";
		$headers .= "From: {$data['headers']['from_name']} <{$data['headers']['from']}>\r\n";

		return mail($data['email'], $data['asunto'], $contenido, $headers);
	}

	/**
    * Crea o sobrescribe un archivo dentro del directorio /storage con los datos proporcionados.
    *
    * @param string $file Nombre del archivo (con extensión).
    * @param string $data Contenido que se escribirá en el archivo.
    * @return void
    */
	public function create_file(string $file, string $data) {
		// Abrir el archivo en modo de escritura ("w")
		$handle = fopen(__DIR__ . '/../storage/' . $file, "w");
		// Escribir los datos en el archivo
		fwrite($handle, $data);
		// Cerrar el archivo
		fclose($handle);
	}

   /**
    * Redirige al home si el sistema ya está instalado (detectado por presencia de archivos `.lock` y `.key`).
    *
    * @return void
    */
	public function isInstalled() {
		if (file_exists(__DIR__.'/../storage/.lock') AND file_exists(__DIR__.'/../storage/.key')) {
			header("Location: ../");
			exit;
		}
	}

	/**
    * Verifica si el método de la solicitud actual es POST.
    *
    * @return bool True si es POST, false en caso contrario.
    */
	public function isMethodPost() {
		return ($_SERVER['REQUEST_METHOD'] === 'POST');
	}

	/**
	 * Reemplaza los valores en el archivo de configuración basado en un array asociativo.
	 *
	 * @param string $templatePath Ruta del archivo con placeholders.
	 * @param array $replacements Arreglo con claves => valores a reemplazar.
	 * @return bool|string Devuelve el contenido reemplazado o false en caso de error.
	 */
	public function renderConfigTemplate(string $templatePath, array $replacements) {
		if (!file_exists($templatePath)) return false;
		$content = file_get_contents($templatePath);
		foreach ($replacements as $key => $infoData) {
			if (!isset($key)) return false;
			$content = str_replace('{{' . $key . '}}', $infoData, $content);
		}
		return $content;
	}

	/**
    * Limpia y valida entradas del usuario, permitiendo sanitización individual o múltiple.
    *
    * - Soporta los campos predefinidos: email, url, password, username, step, license.
    * - Los valores no definidos usan FILTER_SANITIZE_SPECIAL_CHARS como valor por defecto.
    *
    * @param array|string $inputs Clave o arreglo de claves a filtrar.
    * @param int $type Tipo de entrada (INPUT_POST o INPUT_GET). Por defecto: INPUT_POST.
    * @return mixed Valor filtrado o arreglo de valores filtrados.
    */
	public function sanitizer(array|string $inputs, int $type = INPUT_POST) {
		$rules = [
			'email'     => FILTER_VALIDATE_EMAIL,
			'url'       => FILTER_VALIDATE_URL,
			'password'  => FILTER_UNSAFE_RAW,
			'username'  => FILTER_UNSAFE_RAW,
			'step'      => FILTER_SANITIZE_NUMBER_INT,
			'license'   => FILTER_VALIDATE_BOOLEAN,
		];
		if (is_string($inputs)) {
			$filter = $rules[$inputs] ?? FILTER_SANITIZE_SPECIAL_CHARS;
			if($inputs === 'license') {
				return filter_input($type, $inputs, $filter);
			}
			return trim(filter_input($type, $inputs, $filter) ?? '');
		}
		$dataSanitized = [];
		foreach ($inputs as $input) {
			$filter = $rules[$input] ?? FILTER_SANITIZE_SPECIAL_CHARS;
			$dataSanitized[$input] = trim(filter_input($type, $input, $filter) ?? '');
		}
		return $dataSanitized;
	}

}