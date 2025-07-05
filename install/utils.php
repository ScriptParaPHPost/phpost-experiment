<?php 

class Utils {

	private static ?mysqli $db = null;

	# Creando la conexión
	public function dbConnect(array $credentials = []): mysqli {
		if (self::$db instanceof mysqli) return self::$db;

		// Por defecto carga desde config.inc.php
		if (empty($credentials)) {
			$config = require __DIR__ . '/../../config.inc.php';
			$credentials = $config['db'] ?? [];
		}

		// Verifica campos mínimos
		$required = ['hostname', 'username', 'password', 'database'];
		foreach ($required as $key) {
			if (!isset($credentials[$key])) {
				throw new Exception("Falta el parámetro de conexión: $key");
			}
		}

		// Creamos la conexión
		$db = new mysqli(
			$credentials['hostname'],
			$credentials['username'],
			$credentials['password'],
			$credentials['database']
		);

		if ($db->connect_error) {
			throw new Exception("Error al conectar con MySQL: " . $db->connect_error);
		}

		if (!$db->set_charset("utf8mb4")) {
			throw new Exception("Error al configurar el charset: " . $db->error);
		}

		self::$db = $db;
		return self::$db;
	}

	# Otras funciones

	private function secure_url(bool $withoutslashes = true): string {
		$ssl = 'http';
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' || !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') $ssl .= 's';
		return ($withoutslashes ? $ssl . '://' : '');
	}

	public function getUrl(string $page = '', bool $withoutSlashes = true): string {
		$scheme = self::secure_url($withoutSlashes); // devuelve "http://" o "https://"
		$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
		// Aseguramos que el REQUEST_URI tenga slashes UNIX-style
		$uri = str_replace('\\', '/', dirname(dirname($_SERVER['REQUEST_URI'])));
		// Elimina la barra final si se desea
		if ($withoutSlashes) {
			$uri = rtrim($uri, '/');
		}
		return $scheme . $host . $uri . $page;
	}

}