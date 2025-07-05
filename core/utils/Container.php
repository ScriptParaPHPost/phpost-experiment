<?php

declare(strict_types=1);

/**
 * PHPost 2025 - Contenedor de dependencias (Dependency Injection Container).
 *
 * Esta clase permite registrar, resolver y cargar servicios mediante factories (closures).
 * Soporta inyección de dependencias, carga perezosa (lazy loading), resolución automática de DTOs
 * mediante reflexión, y registro en lote de servicios.
 *
 * Basado conceptualmente en PSR-11 (no implementado formalmente).
 *
 * @author      PHPost Team
 * @copyright   2025
 * @link        https://github.com/isidromlc/PHPost
 * @link        https://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
 */

class Container {

   /**
    * Ruta base para localizar archivos del sistema (DTOs, clases, utilidades).
    *
    * Se utiliza como raíz para construir rutas absolutas al cargar archivos PHP.
    * Por defecto apunta a '/core/' dentro del proyecto.
    *
    * @var string
    */
   private string $basePath = TS_ROOT . '/core/';

   /**
    * Instancias de servicios o factories registrados.
    *
    * @var array<string, mixed>
   */
   private array $instances = [];

   /**
    * Mapeo de clases a claves del contenedor.
    *
    * @var array<string, string>
    */
   private array $classMap = [];

   /**
    * Registra un servicio en el contenedor.
    *
    * @param string   $key     Clave única del servicio.
    * @param callable $factory Closure que retorna una instancia del servicio.
    */
   public function set(string $key, callable $factory): void {
      // Guardamos la factory inicialmente
      $this->instances[$key] = $factory;
      // Instanciamos y registramos en el mapa de clases
      $instance = $factory($this);
      $this->classMap[get_class($instance)] = $key;
      // Reemplazamos la factory con la instancia ya resuelta
      $this->instances[$key] = $instance;
   }

   /**
    * Obtiene un servicio por su clave.
    *
    * @param string $key Clave del servicio.
    * @throws Exception Si el servicio no está registrado.
    * @return mixed Instancia del servicio.
    */
   public function get(string $key) {
      if (isset($this->instances[$key])) {
         if (is_callable($this->instances[$key])) {
            $this->instances[$key] = $this->instances[$key]($this);
         }
         return $this->instances[$key];
      }
      throw new Exception("Servicio '$key' no registrado.");
   }

   /**
    * Verifica si existe una clave en el contenedor.
    *
    * @param string $key
    * @return bool
    */
   public function has(string $key): bool {
      return isset($this->instances[$key]);
   }

   /**
    * Carga un archivo PHP, registra un servicio si no existe, y retorna la instancia.
    *
    * @param string        $basepath Ruta relativa (desde /core).
    * @param string        $key      Clave del servicio.
    * @param callable|null $factory  Factory personalizada (opcional).
    * @return mixed Instancia del servicio.
    */
   public function loader(string $basepath, string $key, callable $factory = null) {
      if (!$this->has($key)) {
         require_once $this->loaderPHP($basepath);
         $factory = $factory === NULL ? fn() => new $key() : $factory;
         $this->set($key, $factory);
      }
      return $this->get($key);
   }

   /**
    * Construye la ruta absoluta a un archivo PHP y valida su existencia.
    *
    * @param string $filepath Ruta relativa desde /core.
    * @throws RuntimeException Si el archivo no existe.
    * @return string Ruta absoluta válida.
    */
   public function loaderPHP(string $filepath): string {
      $path = $this->basePath . ltrim($filepath, '/');
      if (!file_exists($path)) {
         throw new RuntimeException("Archivo no encontrado: $path");
      }
      return $path;
   }

   /**
    * Resuelve automáticamente un DTO basado en su nombre,
    * cargando el archivo y resolviendo sus dependencias por tipo.
    *
    * @param string $name Nombre del DTO (sin 'DTO' al final).
    * @throws RuntimeException En caso de archivo o clase inexistente, o si no se pueden resolver dependencias.
    * @return object Instancia del DTO.
    */
   public function resolve(string $className): object {
      $file = $this->basePath . 'dependencies/' . $className . '.php';
      if (!file_exists($file)) {
         throw new RuntimeException("Dependencies no encontrada: $file");
      }
      require_once $file;
      if (!class_exists($className)) {
         throw new RuntimeException("La clase $className no fue definida en el archivo.");
      }
      $reflection = new ReflectionClass($className);
      $constructor = $reflection->getConstructor();
      if (!$constructor) {
         return new $className(); // No necesita dependencias
      }
      $params = [];
      foreach ($constructor->getParameters() as $param) {
         $type = $param->getType();
         if (!$type || $type->isBuiltin()) {
            throw new RuntimeException("No se puede resolver el parámetro \${$param->getName()} en $className.");
         }
         $dependencyName = $type->getName();
         // Lo resolvemos por clase
         $params[] = $this->getByClassName($dependencyName);
      }
      return $reflection->newInstanceArgs($params);
   }

   /**
    * Obtiene una instancia registrada a partir del nombre completo de una clase.
    *
    * @param string $className Nombre completo de la clase (ej. App\Service\Logger).
    * @throws RuntimeException Si no se encuentra una coincidencia.
    * @return object Instancia correspondiente.
    */
   public function getByClassName(string $className): object {
      if (isset($this->classMap[$className])) {
         return $this->get($this->classMap[$className]);
      }
      throw new RuntimeException("No se pudo encontrar una instancia para la clase $className.");
   }

   /**
    * Registra múltiples servicios en lote usando un array de configuración.
    *
    * Cada elemento del array debe contener al menos las claves:
    * - 'file': Ruta al archivo PHP
    * - 'key' : Nombre del servicio
    * - 'fn'  : (Opcional) Factory del servicio. Si no se define, se instancia usando `new $key()`.
    *
    * @param array<int, array{file: string, key: string, fn?: callable}> $containers
    * @throws InvalidArgumentException Si falta 'file' o 'key' en alguno de los elementos.
    * @return void
    */
   public function multiLoader(array $containers = []): void {
      foreach ($containers as $c) {
         if (!isset($c['file'], $c['key'])) {
            throw new InvalidArgumentException("Cada contenedor debe tener 'file' y 'key'.");
         }
         $key = $c['key'];
         $factory = $c['fn'] ?? fn() => new $key();
         $this->loader($c['file'], $key, $factory);
      }
   }
}