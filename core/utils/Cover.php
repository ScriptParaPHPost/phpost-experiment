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

class Cover {

   private string $baseUrl;
   
   private string $basePath;

   private string $baseDefault;

   public function __construct() {
   	$query = db_exec([__FILE__, __LINE__], 'query', "SELECT url FROM w_config_general WHERE tscript_id = 1");
      $this->baseUrl = db_exec('fetch_assoc', $query)['url'];
      $this->basePath = TS_FILES . 'cover/';
      $this->baseDefault = $this->baseUrl . '/views/default/images/imagen_no_disponible.webp';
   }

   public function get(int $pid = 0, ?string $cover = '') {
	   if(!empty($cover)) {
	   	if(is_link($cover)) {
	   	} elseif(is_string($cover)) {
	   	}
   	} else {
   		return $this->baseDefault;
   	}
   }

}