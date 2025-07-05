<?php

declare(strict_types=1);

/**
 * PHPost 2025
 * 
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

class Config {

	protected array $config = [];

	protected string $path;

	public function __construct(string $path = '') {
		$this->path = $path;
	
		if (is_file($path)) {
			$this->config = require $path;
		} elseif (is_dir($path)) {
			foreach (glob($path . '/*.php') as $file) {
				$key = basename($file, '.php');
				$this->config[$key] = require $file;
			}
		} else {
			throw new InvalidArgumentException("La ruta '$path' no es un archivo ni una carpeta válida.");
		}
	}

	public function get(string $key, $default = null) {
		$data = $this->config;
		if (count($data) === 1) {
			$data = reset($data);
		}
		foreach (explode('.', $key) as $segment) {
			if (!isset($data[$segment])) return $default;
			$data = $data[$segment];
		}
		return $data;
	}

}