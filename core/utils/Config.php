<?php

declare(strict_types=1);

/**
 * PHPost 2025
 * 
 * Clase Config
 *
 * Carga archivos de configuración desde un archivo único o un directorio,
 * y permite acceder y modificar datos mediante notación de puntos.
 *
 * Ejemplo:
 *   $config = new Config('ruta/config.php');
 *   $config->get('db.host');
 *   $config->set('mail.from', 'admin@phpost.com');
 *
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

class Config {

   /**
    * @var array Arreglo que contiene todos los datos de configuración cargados.
    */
   protected array $config = [];

   /**
    * @var string Ruta al archivo o carpeta de configuración.
    */
   protected string $path;

   /**
    * Constructor de la clase Config
    *
    * Carga la configuración desde un archivo PHP o desde todos los archivos PHP en una carpeta.
    *
    * @param string $path Ruta al archivo o carpeta de configuración.
    * @throws InvalidArgumentException Si la ruta no es válida.
    */
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

   /**
    * Obtiene un valor desde la configuración usando notación de puntos.
    *
    * @param string $key     Clave con notación de puntos. Ej: 'db.hostname'
    * @param mixed  $default Valor por defecto si no se encuentra la clave.
    * @return mixed          Valor encontrado o el valor por defecto.
    */
   public function get(string $key, $default = null) {
      $data = $this->config;

      // Si solo hay un nivel, lo simplifica
      if (count($data) === 1) {
         $data = reset($data);
      }

      foreach (explode('.', $key) as $segment) {
         if (!isset($data[$segment])) return $default;
         $data = $data[$segment];
      }

      return $data;
   }

   /**
    * Asigna un valor a una clave utilizando notación de puntos.
    *
    * Si la ruta no existe, se crea automáticamente.
    *
    * @param string $key   Clave con notación de puntos. Ej: 'app.theme.color'
    * @param mixed  $value Valor a asignar.
    * @return void
    */
   public function set(string $key, $value): void {
      $segments = explode('.', $key);
      $ref = &$this->config;

      foreach ($segments as $segment) {
         if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
            $ref[$segment] = [];
         }
         $ref = &$ref[$segment];
      }

      $ref = $value;
   }

   /**
    * Devuelve todo el arreglo de configuración completo.
    *
    * @return array Configuración completa cargada en memoria.
    */
   public function all(): array {
      return $this->config;
   }
}
