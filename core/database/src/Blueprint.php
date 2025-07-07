<?php

declare(strict_types=1);

/**
 * PHPost 2025
 * 
 * Clase Blueprint
 * 
 * Representa el plano (esquema) de una tabla en la base de datos.
 * Proporciona una API fluida para definir columnas, tipos de datos,
 * claves primarias, índices y características adicionales de una tabla.
 * 
 * Se utiliza junto con la clase Schema para generar y ejecutar
 * automáticamente sentencias `CREATE TABLE`.
 * 
 * Ejemplo de uso:
 * 
 * Schema::create('users', function(Blueprint $table) {
 *     $table->id();
 *     $table->string('username')->nullable();
 *     $table->string('email');
 *     $table->addIndex(['email']);
 * });
 * 
 * @author PHPost Team
 * @copyright 2025
 * @link http://github.com/isidromlc/PHPost
 * @link http://github.com/joelmiguelvalente
 * @link https://www.linkedin.com/in/joelmiguelvalente/
 */

class Blueprint {

   /**
    * Nombre de la tabla que se va a crear.
    *
    * @var string
    */
   protected string $table;

   /**
    * Lista de columnas definidas para la tabla.
    *
    * @var Column[]
    */
   protected array $columns = [];

   /**
    * Lista de índices adicionales definidos para la tabla.
    *
    * @var string[]
    */
   protected array $indexes = [];

   /**
    * Lista de nombres de índices para evitar duplicados.
    *
    * @var string[]
    */
   protected array $indexNames = [];

   /**
    * Constructor.
    *
    * @param string $table Nombre de la tabla.
    */
   public function __construct(string $table) {
      $this->table = $table;
   }

   /**
    * Agrega una columna `id` entera auto incremental y clave primaria.
    *
    * @param string $name Nombre de la columna (por defecto "id").
    * @return Column
    */
   public function id(string $name = 'id') {
      return $this->integer($name)->autoIncrement()->primary();
   }

   /**
    * Agrega una columna VARCHAR con longitud definida.
    *
    * @param string $name Nombre de la columna.
    * @param int $length Longitud máxima (por defecto 255).
    * @return Column
    */
   public function string(string $name, int $length = 255): Column {
      return $this->addColumn("varchar($length)", $name);
   }

   public function text(string $name): Column {
      return $this->addColumn("text", $name);
   }

   public function char(string $name, int $length = 0): Column {
      return $this->addColumn("char($length)", $name);
   }

   public function tinyText(string $name): Column {
      return $this->addColumn("tinytext", $name);
   }

   public function integer(string $name): Column {
      return $this->addColumn("int", $name);
   }

   public function smallInteger(string $name): Column {
      return $this->addColumn("smallint", $name);
   }

   public function tinyInteger(string $name): Column {
      return $this->addColumn("tinyint(1)", $name);
   }

   public function bigInteger(string $name): Column {
      return $this->addColumn("bigint", $name);
   }

   public function mediumText(string $name): Column {
      return $this->addColumn("mediumtext", $name);
   }

   public function boolean(string $name): Column {
      return $this->addColumn("tinyint(1)", $name);
   }

   public function dateTime(string $name): Column {
      return $this->addColumn("datetime", $name);
   }

   public function float(string $name, int $precision = 8, int $scale = 2): Column {
      return $this->addColumn("float($precision, $scale)", $name);
   }

   public function decimal(string $name, int $precision = 10, int $scale = 2): Column {
      return $this->addColumn("decimal($precision, $scale)", $name);
   }

   public function enum(array $data, string $name): Column {
      foreach($data as $k => $d) {
         $types[$k] = "'$d'";
      }
      $tpe = implode(', ', $types);
      return $this->addColumn("enum($tpe)", $name);
   }

   /**
    * Agrega un índice simple sobre una o más columnas.
    *
    * @param string|array $columns Nombre(s) de columna(s).
    * @param string|null $name Nombre opcional del índice. Se genera automáticamente si no se proporciona.
    * @return void
    * @throws Exception Si no se especifican columnas.
    */
   public function addIndex(string|array $columns, string $name = null) {
      $columns = (array) $columns;
      // Generar nombre único si no hay uno dado
      if (!$name) {
         $nameBase = uniqid(implode('_', $columns));
         $name = $nameBase;
         $counter = 1;
         while (in_array($name, $this->indexNames)) {
            $name = $nameBase . '_' . $counter++;
         }
      }
      // Guardar el nombre para evitar duplicados
      $this->indexNames[] = $name;
      // Validar que las columnas no estén vacías (opcional)
      if (empty($columns)) {
         throw new Exception("No se puede crear índice sin columnas.");
      }
      $colsSql = implode('`, `', $columns);
      $this->indexes[] = "INDEX `$name` (`$colsSql`)";
   }

   /**
    * Método interno para crear y agregar una columna al plano.
    *
    * @param string $type Tipo de dato SQL.
    * @param string $name Nombre de la columna.
    * @return Column
    */
   protected function addColumn(string $type, string $name): Column {
      $column = new Column($type, $name);
      $this->columns[] = &$column;
      return $column;
   }

   /**
    * Genera la sentencia SQL completa para crear la tabla.
    *
    * @return string Consulta SQL `CREATE TABLE` construida a partir del esquema definido.
    */
   public function build(): string {
      $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (\n";
      $definitions = [];
      foreach ($this->columns as $col) {
         $definitions[] = (string) $col;
      }
      foreach ($this->indexes as $idx) {
         $definitions[] = $idx;
      }
      $sql .= implode(",\n", $definitions);
      $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;";
      return $sql;
   }

}