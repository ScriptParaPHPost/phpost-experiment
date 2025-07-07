<?php

declare(strict_types=1);

/**
 * PHPost 2025
 * 
 * Clase Column
 * 
 * Representa una columna individual dentro de una tabla de base de datos.
 * Proporciona métodos encadenables para configurar propiedades comunes
 * de una columna como tipo, nulabilidad, valor por defecto, si es unsigned,
 * auto incrementable o clave primaria.
 * 
 * Al convertir la instancia a string, genera la definición SQL correspondiente.
 * 
 * Ejemplo de uso:
 * 
 * $col = (new Column('INT', 'id'))->unsigned()->autoIncrement()->primary();
 * echo (string)$col; // genera: `id` INT unsigned AUTO_INCREMENT NOT NULL PRIMARY KEY
 * 
 * @author PHPost Team
 * @copyright 2025
 * @link http://github.com/isidromlc/PHPost
 * @link http://github.com/joelmiguelvalente
 * @link https://www.linkedin.com/in/joelmiguelvalente/
 */

class Column {

	/**
	 * Tipo SQL de la columna (ej. INT, VARCHAR(255), TEXT, etc.)
	 *
	 * @var string
	 */
	protected string $type;
	
	/**
	 * Nombre de la columna.
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * Indica si la columna puede ser NULL.
	 *
	 * @var bool
	 */
	protected bool $nullable = false;

	/**
	 * Valor por defecto para la columna.
	 *
	 * @var mixed
	 */
	protected $default = null;

	/**
	 * Indica si la columna es UNSIGNED (solo para tipos numéricos).
	 *
	 * @var bool
	 */
	protected bool $unsigned = false;

	/**
	 * Indica si la columna es AUTO_INCREMENT.
	 *
	 * @var bool
	 */
	protected bool $autoIncrement = false;

	/**
	 * Indica si la columna es PRIMARY KEY.
	 *
	 * @var bool
	 */
	protected bool $primary = false;

	/**
	 * Constructor.
	 *
	 * @param string $type Tipo SQL de la columna.
	 * @param string $name Nombre de la columna.
	 */
	public function __construct(string $type, string $name) {
		$this->type = $type;
		$this->name = $name;
	}

	/**
    * Marca la columna como nullable (acepta valores NULL).
    *
    * @return static
    */
	public function nullable(): static {
		$this->nullable = true;
		return $this;
	}

	/**
    * Establece un valor por defecto para la columna.
    *
    * @param mixed $value Valor por defecto (puede ser string, int, etc.)
    * @return static
    */
	public function default($value): static {
		$this->default = $value;
		return $this;
	}

	/**
    * Marca la columna como unsigned (solo para tipos numéricos).
    *
    * @return static
    */
	public function unsigned(): static {
		$this->unsigned = true;
		return $this;
	}

	/**
    * Marca la columna como AUTO_INCREMENT.
    *
    * @return static
    */
	public function autoIncrement(): static {
		$this->autoIncrement = true;
		return $this;
	}

	/**
    * Marca la columna como PRIMARY KEY.
    *
    * @return static
    */
	public function primary(): static {
		$this->primary = true;
		return $this;
	}

	/**
    * Genera la definición SQL de la columna.
    *
    * @return string Definición SQL lista para incluir en CREATE TABLE.
    */
	public function __toString(): string {
		$sql[] = "`{$this->name}` {$this->type}";
		if ($this->autoIncrement) {
			if (!$this->primary) {
				throw new Exception("AUTO_INCREMENT solo es válido en columnas PRIMARY KEY ({$this->name})");
			}
			$sql[] = "AUTO_INCREMENT";
		}
		if ($this->unsigned) {
			$sql[] = "unsigned";
		}
		$sql[] = $this->nullable ? "NULL" : "NOT NULL";
		if ($this->default !== null) {
			$escaped = is_string($this->default) ? "'{$this->default}'" : $this->default;
			$sql[] = "DEFAULT {$escaped}";
		}
		if ($this->primary) {
			$sql[] = "PRIMARY KEY";
		}
		return implode(' ', $sql);
	}
}
