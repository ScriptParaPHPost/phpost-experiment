<?php 

/**
 * Remaster Taringa - Proyecto de modernización de PHPost
 * 
 * Seguridad básica para limpiar solicitudes HTTP
 * 
 * @author    Miguel92
 * @license   MIT
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
 */

class Paginator {

   private string $baseUrl = '';

   private int $total = 0;

   private int $perPage = 10;

   public function __construct(string $baseUrl = '', int $total = 0, int $perPage = 10) {
      $this->baseUrl = $this->getBaseUrl($baseUrl);
      $this->total = $total;
      $this->perPage = $perPage;
   }

   /**
    * Devuelve el número de página actual.
    */
   private function getBaseUrl(string $path = ''): string {
   	$query = db_exec([__FILE__, __LINE__], 'query', "SELECT url FROM w_config_general WHERE tscript_id = 1");
	   $baseUrl = db_exec('fetch_assoc', $query);
	   return "{$baseUrl['url']}$path";
	}

   /**
    * Devuelve el número de página actual.
    */
   public function getCurrentPage(): int {
      return isset($_GET['page']) ? max((int) $_GET['page'], 1) : 1;
   }

   /**
    * Devuelve el offset y límite SQL.
    */
   public function getLimit(): string {
      $page = $this->getCurrentPage();
      $start = ($page - 1) * $this->perPage;
      return "$start,{$this->perPage}";
   }

   /**
    * Devuelve el número total de páginas.
    */
   public function getTotalPages(): int {
      return (int) ceil($this->total / $this->perPage);
   }

   public function getPages(): array {
      $pages = ceil($this->total / $this->perPage);
      $current = max(1, min((int)($_GET['page'] ?? 1), $pages));
      return [
         'current' => $current,
         'pages' => $pages,
         'prev' => max(1, $current - 1),
         'next' => min($pages, $current + 1),
         'total' => $this->total,
      ];
   }

   /**
    * Genera la paginación HTML.
    */
   public function render(): string {
      $html = '<nav class="pagination">';
      $totalPages = $this->getTotalPages();
      $currentPage = $this->getCurrentPage();

      for ($i = 1; $i <= $totalPages; $i++) {
         if ($i === $currentPage) {
            $html .= "<div class=\"page-item\"><span class=\"page-numbers current\">$i</span></div>";
         } else {
            $html .= "<div class=\"page-item\"><a class=\"page-numbers\" href=\"{$this->baseUrl}/pagina$i\">$i</a></div>";
         }
      }

      return $html . '</nav>';
   }
}

/*$total = 56;
$paginator = new Paginator("/fotos", $total, 10);
$limit = $paginator->getLimit();
$pages = $paginator->getPages();
$html_paginacion = $paginator->render();
echo $html_paginacion;*/