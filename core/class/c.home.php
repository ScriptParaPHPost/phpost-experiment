<?php

declare(strict_types=1);

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

if (!defined('PHPOST_CORE_LOADED')) {
	exit('Acceso denegado: ¡No puedes acceder este script directamente!');
}

class tsHome {

	protected tsCore $tsCore;

	protected tsUser $tsUser;

	protected Paginator $Paginator;

	protected Junk $Junk;

	protected Avatar $Avatar;

	protected Cover $Cover;

	public function __construct( appGlobal $deps, Cover $Cover) {
      $this->tsCore = $deps->tsCore;
      $this->tsUser = $deps->tsUser;
      $this->Paginator = $deps->Paginator;
      $this->Junk = $deps->Junk;
      $this->Avatar = $deps->Avatar;
      $this->Cover = $Cover;
	}

	private function buildCategoryFilter(?string $category): string {
		if (!$category) return '';
		$category = $this->tsCore->setSecure($category);

		$cat = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT cid FROM p_categorias WHERE c_seo = '$category' LIMIT 1"));

		return isset($cat['cid']) && $cat['cid'] > 0 ? "AND p.post_category = " . (int)$cat['cid'] : '';
	}

	private function buildUserFilter(string $append = ''): string {
		$see_mod = (int)db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT c_see_mod FROM w_config_users WHERE tscript_id = 1"))['c_see_mod'];
		if ($this->tsUser->is_admod && $see_mod === 1) return 'p.post_id > 0';
		return "p.post_status = 0 AND u.user_activo = 1 AND u.user_baneado = 0 $append";
	}

	private function isSticky(string $category, string|int $limit, int $sticky = 0): array {
		$where = $this->buildCategoryFilter($category);
		$filter = $this->buildUserFilter();
		$order = ((int)$sticky === 1) ? 'sponsored' : 'id';
		$data = result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT p.post_id, p.post_user, p.post_category, p.post_title, p.post_date, p.post_portada, p.post_comments, p.post_puntos, p.post_private, p.post_sponsored,p.post_status, p.post_sticky, u.user_id, u.user_name, u.user_activo, u.user_baneado,c.c_nombre, c.c_seo, c.c_img FROM p_posts AS p LEFT JOIN u_miembros AS u ON p.post_user = u.user_id LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE {$filter} AND p.post_sticky = $sticky {$where} ORDER BY p.post_$order DESC LIMIT {$limit}"));
		$this->Junk->postArrayContent($data, $this->Cover);
		return $data;
	}

	public function getStickyPosts(?string $category = null, int $limit = 10): array {
		return $this->isSticky($category, $limit, 1);
	}

	public function getNormalPosts(?string $category = null): array {
		$c_where = $this->buildCategoryFilter($category);
		$u_filter = $this->buildUserFilter();
		// Total para paginación
		$this->Paginator->max = (int)db_exec('fetch_row', db_exec([__FILE__, __LINE__], 'query', "SELECT COUNT(p.post_id) AS total FROM p_posts AS p LEFT JOIN u_miembros AS u ON p.post_user = u.user_id WHERE {$u_filter} AND p.post_sticky = 0 {$c_where}"))[0];
		$this->Paginator->limit = (int)db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT c_max_posts FROM w_config_limits WHERE tscript_id = 1"))['c_max_posts'];
		// Consulta de datos
		return [
			'data' => $this->isSticky($category, $this->Paginator->getSqlLimit()), 
			'pages' => $this->Paginator->renderPagination(), 
			'current' => $this->Paginator->getMetadata()['current'],
			'total' => $this->Paginator->max
		];
	}

    /*
        getCatData()
        :: OBTENER DATOS DE UNA CATEGORIA
    */
   public function getInfoCategory() {
      $cat = $this->tsCore->setSecure($_GET['cat']) ?? '';
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT c_nombre, c_seo FROM p_categorias WHERE c_seo = '$cat' LIMIT 1"));
		return $data;
    }
}