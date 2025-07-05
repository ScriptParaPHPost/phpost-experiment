<?php

declare(strict_types=1);

/**
 * PHPost 2025
 * 
 * Contenedor de dependencias para gestionar servicios y sus instancias.
 * 
 * Este contenedor permite registrar servicios usando funciones anónimas (factories)
 * y luego obtenerlos con lazy-loading (carga diferida).
 * 
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

class Container {

	/**
    * Lista de servicios registrados e instancias resueltas.
    *
    * @var array<string, mixed>
    */
   private array $instances = [];

   /**
    * Registra un servicio en el contenedor.
    * 
    * @param string   $key     Nombre o identificador del servicio.
    * @param callable $factory Función anónima que devuelve una instancia del servicio.
    *                          Esta función puede recibir el contenedor como parámetro.
    * 
    * @return void
    */
   public function set(string $key, callable $factory): void {
      $this->instances[$key] = $factory;
   }

   /**
    * Obtiene un servicio desde el contenedor.
    * Si es un factory, se ejecuta solo una vez y se guarda su resultado.
    *
    * @param string $key Nombre del servicio.
    * 
    * @throws Exception Si el servicio no está registrado.
    * 
    * @return mixed Instancia del servicio.
    */
   public function get(string $key) {
      if (isset($this->instances[$key])) {
         // Si es un factory (closure), lo ejecuta una sola vez y guarda la instancia
         if (is_callable($this->instances[$key])) {
            $this->instances[$key] = $this->instances[$key]($this);
         }
         return $this->instances[$key];
      }

      throw new Exception("Servicio '$key' no registrado.");
   }

}