<?php

/**
 * Clase DB - Conexión y operaciones básicas con PDO
 * 
 * Estilo Medoo, pero sin dependencias externas ni Composer.
 * Provee métodos simples para SELECT, INSERT, UPDATE y DELETE
 * usando prepared statements para mayor seguridad.
 * 
 * Ejemplo de uso:
 * 
 * $db = new DB([
 *   'hostname' => 'localhost',
 *   'username' => 'root',
 *   'password' => '',
 *   'database' => 'phpost',
 *   'charset'  => 'utf8mb4'
 * ]);
 * 
 * $posts = $db->select('p_posts', '*', ['post_user' => 1]);
 * $id = $db->insert('p_posts', ['post_title' => 'Hola', 'post_user' => 1]);
 * $db->update('p_posts', ['post_title' => 'Editado'], ['post_id' => $id]);
 * $db->delete('p_posts', ['post_id' => $id]);
 * 
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

class DB {

	/**
    * @var PDO Conexión PDO activa
    */
   protected PDO $PDO;

   /**
    * Constructor: inicializa la conexión con PDO.
    *
    * @param array $config Debe incluir hostname, username, password, database y opcional charset.
    */
   public function __construct(array $config) {
      $options = [
         PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::ATTR_EMULATE_PREPARES   => false,
      ];
      $this->PDO = new PDO($this->DSN($config), $config['username'], $config['password'], $options);
   }

   /**
    * Construye el DSN para PDO.
    *
    * @param array $config
    * @return string
    */
   private function DSN(array $config): string {
   	return sprintf(
         'mysql:host=%s;dbname=%s;charset=%s',
         $config['hostname'],
         $config['database'],
         $config['charset'] ?? 'utf8mb4'
      );
   }

   /**
    * Ejecuta una consulta preparada.
    *
    * @param string $sql Consulta SQL con placeholders
    * @param array $params Valores a enlazar
    * @return PDOStatement
    */
   public function query(string $sql, array $params = []): PDOStatement {
      $stmt = $this->PDO->prepare($sql);
      $stmt->execute($params);
      return $stmt;
   }

   /**
    * Realiza un SELECT desde la base de datos.
    *
    * @param string $table Nombre de la tabla
    * @param string|array $columns Columnas a seleccionar
    * @param array $where Condiciones WHERE (clave => valor)
    * @return array Resultado como arreglo asociativo
    */
   public function select(string $table, string|array $columns = '*', array $where = []): array {
      $cols = is_array($columns) ? implode(', ', $columns) : $columns;
      $sql = "SELECT $cols FROM `$table`";

      if (!empty($where)) {
         $conditions = [];
         foreach ($where as $key => $value) {
            $conditions[] = "`$key` = :$key";
         }
         $sql .= ' WHERE ' . implode(' AND ', $conditions);
      }

      return $this->query($sql, $where)->fetchAll();
   }

   /**
    * Inserta un nuevo registro en una tabla.
    *
    * @param string $table Nombre de la tabla
    * @param array $data Datos en formato clave => valor
    * @return int ID insertado
    */
   public function insert(string $table, array $data): int {
      $columns = implode(', ', array_keys($data));
      $placeholders = implode(', ', array_map(fn($k) => ":$k", array_keys($data)));
      $sql = "INSERT INTO `$table` ($columns) VALUES ($placeholders)";
      $this->query($sql, $data);
      return (int)$this->PDO->lastInsertId();
   }

   /**
    * Actualiza registros en la base de datos.
    *
    * @param string $table Nombre de la tabla
    * @param array $data Datos a actualizar
    * @param array $where Condiciones WHERE
    * @return int Cantidad de filas afectadas
    */
   public function update(string $table, array $data, array $where): int {
   	if (empty($where)) {
    throw new InvalidArgumentException("La cláusula WHERE no puede estar vacía.");
		}
      $set = implode(', ', array_map(fn($k) => "`$k` = :set_$k", array_keys($data)));
      $cond = implode(' AND ', array_map(fn($k) => "`$k` = :where_$k", array_keys($where)));

      $params = [];
      foreach ($data as $k => $v) $params["set_$k"] = $v;
      foreach ($where as $k => $v) $params["where_$k"] = $v;

      $sql = "UPDATE `$table` SET $set WHERE $cond";
      return $this->query($sql, $params)->rowCount();
   }

   /**
    * Elimina registros de una tabla.
    *
    * @param string $table Nombre de la tabla
    * @param array $where Condiciones WHERE
    * @return int Cantidad de filas eliminadas
    */
   public function delete(string $table, array $where): int {
      $cond = implode(' AND ', array_map(fn($k) => "`$k` = :$k", array_keys($where)));
      $sql = "DELETE FROM `$table` WHERE $cond";
      return $this->query($sql, $where)->rowCount();
   }

 	/**
    * Devuelve la instancia de PDO por si se necesita acceso directo.
    *
    * @return PDO
    */
   public function getPDO(): PDO {
      return $this->PDO;
   }
}