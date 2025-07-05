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
	exit('No se permite el acceso directo al script');

require_once __DIR__ . '/../smarty/autoload.php';

class tsSmarty extends \Smarty\Smarty {

	/** @var array<string, string> Directorios adicionales de plantillas */
	public array $templateDirs = [];

	/** @var string Página actual a renderizar */
	public string $page = '';
	public string $tema = '';

	/** @var string Plantilla de error por defecto */
	public string $template_error = 't.error.tpl';

	/**
	 * Constructor principal.
	 * Configura Smarty, sus directorios y plugins personalizados.
	 */
	public function __construct(string $tema) {
		parent::__construct();
		$this->tema = $tema ?? 'default';
	
		$this->setCompileCheck(true);
		$this->setCompileDir(__DIR__ . '/../../storage/cache/' . $this->tema);

		$this->loadPlugins();

		require_once __DIR__ . '/../extensiones/baseExtensiones.php';
		$this->addExtension(new \inc\extensiones\baseExtensiones());

		$this->muteUndefinedOrNullWarnings();
	}

	/**
	 * Carga plugins personalizados de tipo `function` y `modifier` automáticamente.
	 */
	private function loadPlugins(): void {
		$pluginDirs = [
			'function'  => __DIR__ . '/../plugins/function.*.php',
			'modifier'  => __DIR__ . '/../plugins/modifier.*.php',
		];
		foreach ($pluginDirs as $type => $pattern) {
			foreach (glob($pattern) as $file) {
				require_once $file;
				$pluginName = explode('.', basename($file, '.php'))[1];
				$this->registerPlugin($type, $pluginName, "smarty_{$type}_{$pluginName}");
			}
		}
	}

	/**
	 * Aplica un filtro de salida para eliminar espacios innecesarios del HTML.
	 *
	 * @param bool $loadFilter Determina si aplicar el filtro `trimwhitespace`
	 */
	public function output(bool $loadFilter = false): void {
		if ($loadFilter) {
			$this->loadFilter('output', 'trimwhitespace');
		}
	}

	/**
	 * Carga recursivamente todos los subdirectorios desde /templates y los registra como fuentes válidas para Smarty.
	 */
	public function loaderTemplates(): void {
		$basePath = TS_ROOT . '/views/' . TS_TEMA . '/templates';
		$directories = ['templates' => $basePath];

		$scan = function (string $dir) use (&$directories, &$scan, $basePath): void {
			foreach (scandir($dir) as $item) {
				if ($item === '.' || $item === '..') continue;

				$path = $dir . DIRECTORY_SEPARATOR . $item;
				if (is_dir($path)) {
					$relative = str_replace($basePath . '/', '', $path);
					$key = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
					$directories[$key] = $path;
					$scan($path);
				}
			}
		};

		$scan($basePath);
		$this->templateDirs = $directories;
		$this->addTemplateDir($this->templateDirs);
	}

	/**
	 * Intenta cargar y renderizar la plantilla actual. Si falla, se muestra un mensaje amigable.
	 */
	public function loader(): void {
		$page = "t.{$this->page}.tpl";
		try {
			$template = $this->templateExists($page) ? $page : $this->template_error;
			$this->display($template);
		} catch (\Exception $e) {
			$this->setError($e, $page);
		}
	}

	/**
	 * Limpia el archivo compilado de la plantilla indicada.
	 *
	 * @param string $template Plantilla a limpiar del caché de compilación
	 */
	public function clearCompiled(string $template): void {
		$this->clearCompiledTemplate($template);
	}

	/**
	 * Muestra un error detallado si ocurre una excepción al cargar la plantilla.
	 *
	 * @param \Throwable $e Objeto de excepción capturado
	 * @param string $page Nombre de la plantilla que causó el error
	 */
	private function setError(\Throwable $e, string $page): void {
		$message = preg_replace_callback("/'([^']+)'/", fn($m) => "'<strong>{$m[1]}</strong>'", $e->getMessage());
		$html = "Lo sentimos, se produjo un error al cargar la plantilla <strong>$page</strong>.<br>
		<br>Motivo:<br><code style=\"font-size:1rem;line-height:1.3rem;color:#d971ad;background:rgba(217,113,173,0.12);display:block;padding:.5em;\">$message</code>";
		show_error($html, 'plantilla');
	}

}