<?php

/**
 * PHPost 2025 - Gestión segura de contraseñas
 * 
 * Esta clase ofrece funciones para hashear, verificar y validar contraseñas
 * utilizando `password_hash()` y `password_verify()` con opciones seguras.
 * 
 * @author      PHPost Team
 * @copyright   2025
 * @link        https://github.com/isidromlc/PHPost
 * @link        https://github.com/joelmiguelvalente
 * @link        https://linkedin.com/in/joelmiguelvalente
 */

class PasswordManager {

	/**
	 * Costo de procesamiento para bcrypt (10–13 recomendado).
	 * @var int
	 */
	private int $cost;

	/**
	 * Constructor
	 * 
	 * @param int $cost Nivel de costo (más alto = más seguro pero más lento)
	 * @throws InvalidArgumentException
	 */
	public function __construct(int $cost = 12) {
	   if ($cost < 10 || $cost > 13) {
	      throw new InvalidArgumentException("El costo debe estar entre 10 y 13.");
	   }
	   $this->cost = $cost;
	}

	/**
	 * Hashea una contraseña utilizando BCRYPT.
	 * 
	 * @param string $password Contraseña sin hashear
	 * @return string Contraseña hasheada
	 */
	public function hash(string $password): string {
	   return password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->cost]);
	}

	/**
	 * Verifica una contraseña contra un hash almacenado.
	 * 
	 * @param string $password Contraseña introducida
	 * @param string $hashedPassword Contraseña hasheada almacenada
	 * @return bool true si coinciden, false en caso contrario
	 */
	public function verify(string $password, string $hashedPassword): bool {
	   return password_verify($password, $hashedPassword);
	}

	/**
	 * Verifica si el hash actual necesita ser actualizado.
	 * 
	 * @param string $hashedPassword Contraseña hasheada
	 * @return bool true si necesita ser rehasheada
	 */
	public function needsRehash(string $hashedPassword): bool {
	   return password_needs_rehash($hashedPassword, PASSWORD_BCRYPT, ['cost' => $this->cost]);
	}

	/**
	 * Valida la fortaleza de una contraseña.
	 * 
	 * Verifica:
	 * - Longitud mínima de 8 caracteres
	 * - Al menos una letra mayúscula
	 * - Al menos un número
	 * - Al menos un símbolo
	 * 
	 * @param string $password Contraseña a validar
	 * @return array Lista de errores. Vacío si es válida.
	 */
	public function validatePassword(string $password): string {
	   $errors = '';

	   if (strlen($password) < 8) {
	      $errors = "La contraseña debe tener al menos 8 caracteres.";
	   } elseif (!preg_match('/[A-Z]/', $password)) {
	      $errors = "Debe contener al menos una letra mayúscula.";
	   } elseif (!preg_match('/\d/', $password)) {
	      $errors = "Debe contener al menos un número.";
	   } elseif (!preg_match('/[^a-zA-Z0-9]/', $password)) {
	      $errors = "Debe contener al menos un símbolo.";
	   }

	   return $errors;
	}
}