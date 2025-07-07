<?php 

declare(strict_types=1);

/**
 * PHPost 2025
 *
 * Clase Migrator
 *
 * Esta clase se encarga de gestionar la ejecución automática de archivos
 * de migración de base de datos. Ejecuta las migraciones que aún no han sido
 * aplicadas y mantiene un registro de las ya ejecutadas en la tabla `migrations_tbls`.
 *
 * Características:
 * - Detecta automáticamente archivos PHP de migraciones.
 * - Evita ejecutar dos veces la misma migración.
 * - Ejecuta cada migración dentro de una transacción segura.
 * - Registra errores de forma clara y reversible.
 * 
 * Ejemplo de uso:
 *
 * $migrator = new Migrator($pdo);
 * $migrator->run();
 *
 * Los archivos en `/migrations/*.php` deben retornar una instancia de una clase que extienda `Migration`
 * y defina el método `up()`.
 *
 * @author PHPost Team
 * @copyright 2025
 * @link http://github.com/isidromlc/PHPost
 * @link http://github.com/joelmiguelvalente
 * @link https://www.linkedin.com/in/joelmiguelvalente/
 */

class Migrator {

   /**
    * Instancia PDO para conexión a base de datos.
    *
    * @var PDO
    */
   protected PDO $pdo;

   /**
    * Ruta absoluta a la carpeta de migraciones.
    *
    * @var string
    */
   protected string $migrationPath;

   /**
    * Constructor.
    *
    * @param PDO $pdo Conexión PDO activa.
    * @param string $path Ruta relativa a la carpeta de migraciones (por defecto: /../migrations).
    */
   public function __construct(PDO $pdo, string $path = '/../migrations') {
      $this->pdo = $pdo;
      $this->migrationPath = __DIR__ . $path;
      $this->ensureMigrationsTable();
   }

   /**
    * Ejecuta todas las migraciones que aún no han sido aplicadas.
    *
    * @return void
    */
   public function run(bool $refresh = false): void {
      foreach (glob("{$this->migrationPath}/*.php") as $file) {
         $name = basename($file, '.php');

         $migration = require $file;
         if (!$migration instanceof Migration) {
            continue;
         }
         if ($refresh || !$this->alreadyMigrated($name)) {
            try {
               $this->pdo->beginTransaction();
               if ($refresh) {
                  $migration->down();
               }
               $migration->up();
               if (!$refresh) {
                  $this->markAsMigrated($name);
               } else {
                  $this->clearMigrationRecord($name);
               }
               $this->pdo->commit();
               echo $refresh ? "[↻] Refrescada: $name\n" : "[↑] Migrada: $name\n";
            } catch (Exception $e) {
               if ($this->pdo->inTransaction()) {
                  $this->pdo->rollBack();
               }
               echo "[✘] Error en $name: " . $e->getMessage() . "\n";
            }
         } else {
            echo "[✔] $name ya migrada\n";
         }
      }
   }

   /**
    * Verifica si una migración ya fue ejecutada.
    *
    * @param string $name Nombre base del archivo de migración.
    * @return bool
    */
   protected function alreadyMigrated(string $name): bool {
      $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM migrations_tbls WHERE name = ?");
      $stmt->execute([$name]);
      return $stmt->fetchColumn() > 0;
   }

   /**
    * Registra una migración como ejecutada.
    *
    * @param string $name Nombre de la migración.
    * @return void
    */
   protected function markAsMigrated(string $name): void {
      $stmt = $this->pdo->prepare("INSERT INTO migrations_tbls (name, migrated_at) VALUES (?, NOW())");
      $stmt->execute([$name]);
   }

   protected function clearMigrationRecord(string $name): void {
      $stmt = $this->pdo->prepare("DELETE FROM migrations_tbls WHERE name = ?");
      $stmt->execute([$name]);
   }

   /**
    * Crea la tabla de control `migrations_tbls` si aún no existe.
    *
    * @return void
    */
   protected function ensureMigrationsTable(): void {
      $this->pdo->exec("
         CREATE TABLE IF NOT EXISTS migrations_tbls (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) UNIQUE NOT NULL,
            migrated_at DATETIME NOT NULL
         );
      ");
   }
}