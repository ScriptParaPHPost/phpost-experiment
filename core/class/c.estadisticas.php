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

if (!defined('PHPOST_CORE_LOADED')) 
	exit('Acceso denegado: ¡No puedes acceder este script directamente!');

class tsEstadisticas {

	protected tsCore $tsCore;
	protected Junk $Junk;

	public function __construct(Simple $deps) {
		$this->tsCore = $deps->tsCore;
		$this->Junk = $deps->Junk;		
	}

	private function getStatsData(): array {
		return db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', 
			"SELECT stats_max_online, stats_max_time, stats_time, stats_time_cache, stats_miembros, stats_posts, stats_fotos, stats_comments, stats_foto_comments 
			 FROM w_stats WHERE stats_no = 1"
		));
	}

	private function getConfig(): array {
		return db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', 
			"SELECT c_count_guests, c_last_active, c_stats_cache FROM w_config_users WHERE tscript_id = 1"
		));
	}

	private function verifyStats(array &$return): void {
		$queries = [
			'miembros'       => "SELECT COUNT(user_id) FROM u_miembros WHERE user_activo = 1 AND user_baneado = 0",
			'posts'          => "SELECT COUNT(post_id) FROM p_posts WHERE post_status = 0",
			'fotos'          => "SELECT COUNT(foto_id) FROM f_fotos WHERE f_status = 0",
			'comments'       => "SELECT COUNT(cid) FROM p_comentarios WHERE c_status = 0",
			'foto_comments'  => "SELECT COUNT(cid) FROM f_comentarios"
		];

		foreach ($queries as $key => $sql) {
			$return["stats_$key"] = (int)db_exec('fetch_row', db_exec([__FILE__, __LINE__], 'query', $sql))[0];
		}
	}

	private function getOnlineQuery(bool $countGuests, int $activeLimit): string {
		return $countGuests 
			? "SELECT COUNT(user_id) FROM u_miembros WHERE user_lastactive > $activeLimit" 
			: "SELECT COUNT(DISTINCT session_ip) FROM u_sessions WHERE session_time > $activeLimit";
	}

	public function get(): array {
		$now = $this->Junk->setTime();
		$stats = $this->getStatsData();
		$config = $this->getConfig();
		$updateParts = [];

		// Validar caché
		if ((int)$stats['stats_time_cache'] < $now - ((int)$config['c_stats_cache'] * 60)) {
			$this->verifyStats($stats);
			$updateParts[] = "stats_time_cache = $now";
			$updateParts[] = "stats_miembros = {$stats['stats_miembros']}";
			$updateParts[] = "stats_posts = {$stats['stats_posts']}";
			$updateParts[] = "stats_fotos = {$stats['stats_fotos']}";
			$updateParts[] = "stats_comments = {$stats['stats_comments']}";
			$updateParts[] = "stats_foto_comments = {$stats['stats_foto_comments']}";
		}

		// Usuarios online
		$activeLimit = $now - ((int)$config['c_last_active'] * 60);
		$queryOnline = $this->getOnlineQuery((int)$config['c_count_guests'] === 1, $activeLimit);
		$stats['stats_online'] = (int)db_exec('fetch_row', db_exec([__FILE__, __LINE__], 'query', $queryOnline))[0];

		// Nuevo récord de online
		if ($stats['stats_online'] > (int)$stats['stats_max_online']) {
			$updateParts[] = "stats_max_online = {$stats['stats_online']}";
			$updateParts[] = "stats_max_time = $now";
		}

		// Siempre actualizar stats_time
		$updateParts[] = "stats_time = $now";

		// Ejecutar UPDATE solo si hay algo para actualizar
		if (!empty($updateParts)) {
			db_exec([__FILE__, __LINE__], 'query', "UPDATE w_stats SET " . implode(', ', $updateParts));
		}

		return $stats;
	}
}