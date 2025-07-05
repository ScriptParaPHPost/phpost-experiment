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

class Avatar {

   private string $baseUrl;
   
   private string $basePath;

   public function __construct() {
   	$query = db_exec([__FILE__, __LINE__], 'query', "SELECT url FROM w_config_general WHERE tscript_id = 1");
      $this->baseUrl = db_exec('fetch_assoc', $query)['url'];
      $this->basePath = TS_FILES . 'avatar/';
   }

   public function get(int $uid, string $nick = '', string $filename = 'default'): string {
      $pathUid = "UID_{$uid}";
      $dirPath = $this->basePath . $pathUid;
      $avatarFile = $dirPath . '/' . $filename . '.webp';
      $avatarUrl  = "{$this->baseUrl}/storage/avatar/{$pathUid}/{$filename}.webp";

      // Si ya existe el avatar
      if (is_dir($dirPath) && file_exists($avatarFile)) {
         return $avatarUrl;
      }

      // Crear carpeta si no existe
      if (!is_dir($dirPath)) {
         mkdir($dirPath, 0777, true);
         chmod($dirPath, 0777);
      }

      // Generar avatar desde API externa
      $avatarFrom = "https://ui-avatars.com/api/?name=" . urlencode($nick) . "&background=random&size=180&font-size=0.60&length=2&format=webp";
      copy($avatarFrom, $avatarFile);

      return $avatarUrl;
   }

}