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

class tsEmail {

	private array $mail;

	public string $asunto;

	public string $plantilla;

	public function __construct(array $mail = []){
		$this->mail = $mail;
	}

	private function getTemplateVariables(string $contenido): array {
		preg_match_all('/{{\s*(\w+)\s*}}/', $contenido, $matches);
		return array_unique($matches[1]);
	}

	/**
	 * Lee y procesa la plantilla, reemplazando los valores con los datos.
	 */
	private function getTemplateContent(array $data): string {
		$rutaPlantilla = __DIR__ . '/../templates/' . $this->plantilla . '.html';
		if (!file_exists($rutaPlantilla)) {
			throw new RuntimeException("La plantilla '{$this->plantilla}' no existe.");
		}
		$contenido = file_get_contents($rutaPlantilla);
		$necesarios = $this->getTemplateVariables($contenido);
		foreach ($necesarios as $clave) {
			if (!array_key_exists($clave, $data)) {
				throw new InvalidArgumentException("Falta el dato requerido: '$clave'");
			}
			$contenido = str_replace('{{'.$clave.'}}', htmlspecialchars((string)$data[$clave], ENT_QUOTES, 'UTF-8'), $contenido);
		}
		return $contenido;
	}

	/**
	 * Genera los headers del correo.
	 */
	private function setEmailHeaders(): string {
		$sender = "{$this->mail['from_name']} <{$this->mail['from']}>";

		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: {$this->mail['content_type']}; charset={$this->mail['encoding']}\r\n";
		$headers .= "Content-Transfer-Encoding: 8bit\r\n";
		$headers .= "X-Priority: 1\r\n";
		$headers .= "From: {$sender}\r\n";
		$headers .= "Return-Path: {$sender}\r\n";
		$headers .= "Reply-To: {$sender}\r\n";

		return $headers;
	}

	/**
	 * Envía un correo usando una plantilla HTML.
	 */
	public function enviar(string $email, array $data): bool {
		try {

			$contenido = $this->getTemplateContent($data);
			$asunto = $this->asunto ?: ($this->mail['default_subject'] ?? 'Sin asunto');
			$headers = $this->setEmailHeaders();

			return mail($email, $asunto, $contenido, $headers);

		} catch (Throwable $e) {
			error_log("Error al enviar email a {$email}: " . $e->getMessage());
			return false;
		}
	}
}