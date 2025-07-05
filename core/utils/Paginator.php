<?php

/**
 * PHPost 2025
 * 
 * Clase Paginator
 * Maneja la lógica de paginación de manera segura y flexible.
 * 
 * @author    PHPost Team
 * @license   MIT
 */

class Paginator {

   public int $max;

   public int $limit;

   protected int $currentPage;

   protected string $baseUrl;

   public function __construct() {
		$config = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT url FROM w_config_general WHERE tscript_id = 1"));
   	$this->baseUrl = $config['url'];
   }


   /**
    * Retorna el OFFSET y LIMIT para consultas SQL.
    */
   public function getSqlLimit(bool $useStart = false): string {
      $start = 0;
      if ($useStart) {
         $start = isset($_GET['s']) ? max((int) $_GET['s'], 0) : 0;
         if ($this->exceedsMax($this->limit, $this->max)) {
            $start = 0;
         }
      } else {
         $page = isset($_GET['page']) ? max((int) $_GET['page'], 1) : 1;
         $start = ($page - 1) * $this->limit;
      }
      return "{$start},{$this->limit}";
   }

   /**
    * Retorna datos útiles para manejar la paginación.
    */
   public function getMetadata(): array {
   	if($this->limit <= 0) return [
   		'pages' => 0
   	];
      $totalPages = (int) ceil($this->max / $this->limit);
      $currentPage = isset($_GET['page']) ? max(1, min((int)$_GET['page'], $totalPages)) : 1;

      return [
         'current'  => $currentPage,
         'pages'    => $totalPages,
         'prev'     => max(1, $currentPage - 1),
         'next'     => min($totalPages, $currentPage + 1),
         'max'      => $this->exceedsMax($this->limit, $this->max),
         'section'  => $totalPages + 1
      ];
   }

   /**
    * Verifica si se excede el máximo de resultados permitido.
    */
   public function exceedsMax(int $limit, int $maxResults): bool {
      $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
      $offset = $page * $limit;

      return $maxResults > 0 && $offset > $maxResults;
   }

   /**
    * Devuelve el HTML del índice de paginación.
    */
	public function renderPagination(int $contiguous = 2): string {
	   $meta = $this->getMetadata();
	   $totalPages = $meta['pages'];
	   $currentPage = $meta['current'];
	   if ($totalPages <= 1) return '';
	   $html = '<nav class="pagination flex justify-center items-center gap-1 bg-blue-100 bg-opacity:10 py-3 rounded">';
	   $urlFn = fn(int $page) => $this->baseUrl . (str_contains($this->baseUrl, '?') ? '&' : '?') . 'page=' . $page;
	   $html .= $this->renderPageLink(1, $currentPage, $urlFn); // Siempre mostrar la página 1
	   // Mostrar puntos suspensivos si hay páginas anteriores fuera del rango
	   if ($currentPage - $contiguous > 2) {
	      $html .= $this->dots();
	   }
	   // Mostrar páginas en el rango alrededor de la actual
	   for ($i = max(2, $currentPage - $contiguous); $i <= min($totalPages - 1, $currentPage + $contiguous); $i++) {
	      $html .= $this->renderPageLink($i, $currentPage, $urlFn);
	   }
	   // Mostrar puntos suspensivos si hay páginas posteriores fuera del rango
	   if ($currentPage + $contiguous < $totalPages - 1) {
	      $html .= $this->dots();
	   }
	   // Siempre mostrar la última página si hay más de una
	   if ($totalPages > 1) {
	      $html .= $this->renderPageLink($totalPages, $currentPage, $urlFn);
	   }
	   $html .= '</nav>';
	   return $html;
	}

	private function renderPageLink(int $page, int $current, callable $urlFn): string {
	   if ($page === $current) {
	      return "<div class=\"page-item\"><span class=\"page-numbers current px-3 py-1 rounded bg-blue-800 text-blue-50 hover:bg-blue-950\">$page</span></div>";
	   } else {
	      return $this->link($page, $urlFn($page));
	   }
	}

   protected function link(int $page, string $url): string {
      return "<div class=\"page-item\"><a class=\"page-numbers bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded\" href=\"$url\">$page</a></div>";
   }

   protected function dots(): string {
      return "<div class=\"page-item off\"><span class=\"page-numbers px-3 py-1 rounded bg-neutral-100\">...</span></div>";
   }
}

/*

// Supongamos que obtuviste $total desde la DB
$total = 125;
$currentPage = $_GET['page'] ?? 1;

$paginator = new Paginator($total, 10, (int)$currentPage, '/articulos');

// Para la query SQL:
$limit = $paginator->getSqlLimit(); // "0,10", "10,10", etc

// Para la plantilla Smarty o PHP:
$meta = $paginator->getMetadata();

// Para mostrar la navegación HTML
echo $paginator->renderPagination();

*/