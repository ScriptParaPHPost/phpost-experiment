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

class tsAfiliado {

	protected tsCore $tsCore;

	protected tsUser $tsUser;

	protected Junk $Junk;

	public function __construct(Afiliado $deps) {
		$this->tsCore = $deps->tsCore;
		$this->tsUser = $deps->tsUser;
		$this->Junk = $deps->Junk;
	}

	public function getAfiliados(string $type = 'home'): array {
		$sqlBase = "aid, a_titulo, a_url, a_banner, a_descripcion";
		$where = " WHERE a_active = 1 ORDER BY RAND() LIMIT 5";

		if ($type === 'admin') {
			$sqlBase .= ", a_sid, a_hits_in, a_hits_out, a_date, a_active";
			$where = "";
		}

		return result_array(db_exec([__FILE__, __LINE__], 'query', "SELECT $sqlBase FROM w_afiliados$where"));
	}

	public function getAfiliado(string $type = ''): array {
		$aid = isset($_GET['aid']) ? (int)$_GET['aid'] : (isset($_POST['ref']) ? (int)$_POST['ref'] : 0);
		if ($aid <= 0) return [];

		return db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT aid, a_titulo, a_url, a_banner, a_descripcion FROM w_afiliados WHERE aid = $aid"));
	}

	private function getPostInput(): array {
		return [
			'titulo' => $this->tsCore->parseBadWords($this->tsCore->setSecure($_POST['atitle'] ?? '')),
			'url' => $this->tsCore->parseBadWords($this->tsCore->setSecure($_POST['aurl'] ?? '')),
			'banner' => $this->tsCore->parseBadWords($this->tsCore->setSecure($_POST['aimg'] ?? '')),
			'desc' => $this->tsCore->parseBadWords($this->tsCore->setSecure($_POST['atxt'] ?? '')),
			'sid' => (int) $this->tsCore->parseBadWords($_POST['aID'] ?? 0),
		];
	}

	public function newAfiliado(): string {
		global $tsNotificaciones;

		$dataIn = $this->getPostInput();

		if (empty($dataIn['titulo']) || empty($dataIn['url']) || $dataIn['url'] === 'http://' || empty($dataIn['banner']) || $dataIn['banner'] === 'http://' || empty($dataIn['desc'])) {
			return '2: Faltan datos';
		}

		if (!filter_var($dataIn['url'], FILTER_VALIDATE_URL)) return '0: Url incorrecta';

		if (db_exec([__FILE__, __LINE__], 'query', "INSERT INTO `w_afiliados` (a_titulo, a_url, a_banner, a_descripcion, a_sid, a_date) VALUES ('{$dataIn['titulo']}', '{$dataIn['url']}', '{$dataIn['banner']}', '{$dataIn['desc']}', '{$dataIn['sid']}', {$this->Junk->setTime()})")) {
			$afid = db_exec('insert_id');
			$aviso = '<center><a href="'.$dataIn['url'].'"><img src="'.$dataIn['banner'].'" title="'.$dataIn['titulo'].'"/></a></center><br><br>'.$dataIn['titulo'].' quiere ser su afiliado. Revise la administraci&oacute;n para aceptar o cancelar.';
			$tsNotificaciones->setAviso(1, 'Nueva afiliaci&oacute;n', $aviso, 0);

			$entit = $this->tsCore->settings['titulo'];
			$enurl = $this->tsCore->settings['url'] . '/?ref=' . $afid;
			$enimg = $this->tsCore->settings['banner'];

			return "1: <div class='emptyData'>Afiliaci&oacute;n agregada</div><br>
			<div style='padding:0 35px;'>
				Notificamos al administrador. Mientras tanto, enlazanos con el siguiente c&oacute;digo:<br><br>
				<div class='form-line'>
					<label for='atitle'>C&oacute;digo HTML</label>
					<textarea rows='5' style='width:295px' onclick='this.select()'>
<a href='$enurl' target='_blank' title='$entit'><img src='$enimg'></a>
					</textarea>
				</div>
			</div>";
		}

		return '0: Error inesperado';
	}

	public function EditarAfiliado(): string {
		$dataIn = $this->getPostInput();

		if (!$dataIn['sid'] || !$dataIn['titulo'] || !$dataIn['url'] || !$dataIn['banner'] || !$dataIn['desc']) return '0: Faltan datos';
		if (!filter_var($dataIn['url'], FILTER_VALIDATE_URL)) return '0: Url incorrecta';

		$data = $this->tsCore->getIUP([
			'titulo' => $dataIn['titulo'],
			'url' => $dataIn['url'],
			'banner' => $dataIn['banner'],
			'descripcion' => $dataIn['desc'],
		], 'a_');

		return db_exec([__FILE__, __LINE__], 'query', "UPDATE w_afiliados SET $data WHERE aid = {$dataIn['sid']}")
			? '1: Guardado'
			: '0: Ocurri&oacute; un error';
	}

	public function DeleteAfiliado(int $aid): string {
		if ($this->tsUser->is_admod !== 1) return '0: No autorizado';
		return db_exec([__FILE__, __LINE__], 'query', "DELETE FROM w_afiliados WHERE aid = $aid")
			? '1: Afiliado eliminado'
			: '0: Error al eliminar';
	}

	public function SetActionAfiliado(): string {
		$aid = (int)filter_input(INPUT_POST, 'aid', FILTER_SANITIZE_NUMBER_INT);
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT a_active FROM w_afiliados WHERE aid = $aid"));
		$active = (int)$data['a_active'] === 1 ? 0 : 1;

		return db_exec([__FILE__, __LINE__], 'query', "UPDATE w_afiliados SET a_active = $active WHERE aid = $aid")
			? ($active === 0 ? '2: Afiliado deshabilitado' : '1: Afiliado habilitado')
			: '0: Error al actualizar';
	}

	public function urlOut(): void {
		$aid = (int)filter_input(INPUT_GET, 'ref', FILTER_SANITIZE_NUMBER_INT);
		$data = db_exec('fetch_assoc', db_exec([__FILE__, __LINE__], 'query', "SELECT a_url, a_sid FROM w_afiliados WHERE aid = $aid LIMIT 1"));
		if (!$data || empty($data['a_url'])) {
			$this->tsCore->redirectTo($this->tsCore->settings['url']);
		}
		db_exec([__FILE__, __LINE__], 'query', "UPDATE `w_afiliados` SET a_hits_out = a_hits_out + 1 WHERE aid = $aid");
		$this->tsCore->redirectTo("{$data['a_url']}" . (!empty($data['a_sid']) ? "?ref={$data['a_sid']}" : ''));
	}

	public function urlIn(): void {
		$aid = (int)filter_input(INPUT_GET, 'ref', FILTER_SANITIZE_NUMBER_INT);
		if ($aid > 0) {
			db_exec([__FILE__, __LINE__], 'query', "UPDATE `w_afiliados` SET a_hits_in = a_hits_in + 1 WHERE aid = $aid");
		}
		$this->tsCore->redirectTo($this->tsCore->settings['url']);
	}
}