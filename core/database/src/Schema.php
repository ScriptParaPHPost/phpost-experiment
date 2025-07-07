<?php

declare(strict_types=1);

/**
 * PHPost 2025
 * 
 * Clase Schema
 * 
 * Esta clase proporciona una interfaz estática para manejar la
 * creación y eliminación de tablas en la base de datos usando PDO.
 * Simplifica la definición de esquemas de tablas mediante la 
 * utilización de la clase Blueprint para construir sentencias SQL.
 * 
 * Funcionalidades principales:
 * - Establecer la conexión PDO para la base de datos.
 * - Crear tablas mediante un callback que define su estructura.
 * - Eliminar tablas si existen.
 * 
 * @author PHPost Team
 * @copyright 2025
 * @link http://github.com/isidromlc/PHPost
 * @link http://github.com/joelmiguelvalente
 * @link https://www.linkedin.com/in/joelmiguelvalente/
 */

require_once __DIR__ . '/Column.php';
require_once __DIR__ . '/Blueprint.php';

class Schema {

   /**
    * Instancia PDO compartida para la conexión a la base de datos.
    *
    * @var PDO|null
    */
   protected static ?PDO $pdo = null;

   /**
    * Establece la conexión PDO que se usará para ejecutar las consultas.
    *
    * @param PDO $pdo Instancia de conexión PDO configurada y activa.
    * @return void
    */
   public static function setConnection(PDO $pdo): void {
      self::$pdo = $pdo;
   }

   public static function getPDO(): PDO {
      return self::$pdo;
   }

   /**
    * Crea una tabla nueva usando un callback para definir su esquema.
    * 
    * El callback recibe una instancia de Blueprint para definir las columnas
    * y otros detalles de la tabla. Luego se construye y ejecuta la consulta SQL.
    *
    * @param string $tableName Nombre de la tabla a crear.
    * @param Closure $callback Función que recibe un Blueprint para definir el esquema.
    * @return void
    *
    * @throws RuntimeException Si no se ha establecido la conexión PDO.
    */
   public static function create(string $tableName, Closure $callback): void {
      $blueprint = new Blueprint($tableName);
      $callback($blueprint);
      $sql = $blueprint->build();
      self::execute($sql);
   }

   /**
    * Elimina una tabla si existe en la base de datos.
    *
    * @param string $tableName Nombre de la tabla a eliminar.
    * @return void
    *
    * @throws RuntimeException Si no se ha establecido la conexión PDO.
    */
   public static function dropIfExists(string $tableName): void {
      $sql = "DROP TABLE IF EXISTS `$tableName`;";
      self::execute($sql);
   }

   /**
    * Ejecuta una sentencia SQL en la base de datos.
    *
    * @param string $sql Sentencia SQL a ejecutar.
    * @return void
    *
    * @throws RuntimeException Si la conexión PDO no está configurada.
    * @throws PDOException Si ocurre un error durante la ejecución SQL.
    */
   protected static function execute(string $sql): void {
      if (!self::$pdo) {
         throw new RuntimeException("No se ha establecido una conexión PDO. Usa Schema::setConnection().");
      }
      self::$pdo->exec($sql);
   }

}