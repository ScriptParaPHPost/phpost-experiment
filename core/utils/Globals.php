<?php

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

class Globals {

	public function slugly(string $string = ''): string {
		$string = htmlentities($string, ENT_QUOTES, 'UTF-8');
		$string = preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', $string);
		$string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
		$string = preg_replace('~[^0-9a-z]+~i', '-', $string);
		$string = strtolower(trim($string, '-'));
		return $string;
	}

	public function getVersion(string $type = ''): string {
		$version['version'] = file_get_contents(__DIR__ . '/../../.version');
		$version['code'] = str_replace('.', '_', $version['version']);
		return $version[$type];
	}

	/**
	 * Envía un correo HTML basado en una plantilla.
	 *
	 * @param string $email     Correo del destinatario.
	 * @param array  $data      Datos para reemplazar en la plantilla (ej: ['usuario' => 'Juan']).
	 * @param string $plantilla Nombre del archivo de plantilla (sin extensión).
	 * @param string $asunto    Asunto del correo.
	 * @return bool             true si se envió con éxito, false en caso contrario.
	 */
	public function enviar(string $email, array $data, string $plantilla, string $asunto): bool {
	   $rutaPlantilla = __DIR__ . '/../templates/' . $plantilla . '.html';

	   if (!file_exists($rutaPlantilla)) {
	      throw new RuntimeException("La plantilla '$plantilla' no existe.");
	   }

	   $contenido = file_get_contents($rutaPlantilla);

	   foreach ($data as $clave => $valor) {
	      $contenido = str_replace('{{' . $clave . '}}', $valor, $contenido);
	   }

	   $headers  = "MIME-Version: 1.0\r\n";
	   $headers .= "Content-type: text/html; charset=UTF-8\r\n";
	   $headers .= "From: PHPost <no-reply@phpost.net>\r\n";

	   return mail($email, $asunto, $contenido, $headers);
	}



}