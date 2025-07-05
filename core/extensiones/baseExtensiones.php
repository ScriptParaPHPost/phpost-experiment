<?php 

namespace inc\extensiones;

use Smarty\Extension\Base;
require __DIR__ . DIRECTORY_SEPARATOR . 'GetUrlModifierCompiler.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'HumanModifierCompiler.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'Nl2brModifierCompiler.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'QuotModifierCompiler.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'SeoModifierCompiler.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'TrimModifierCompiler.php';

class baseExtensiones extends Base {

	public function getModifierCompiler(string $modifier): ?\Smarty\Compile\Modifier\ModifierCompilerInterface {

		return match ($modifier) {
			'getUrl' => new \inc\extensiones\GetUrlModifierCompiler,
			'human' => new \inc\extensiones\HumanModifierCompiler,
			'nl2br' => new \inc\extensiones\Nl2brModifierCompiler,
			'quot' => new \inc\extensiones\QuotModifierCompiler,
			'seo' => new \inc\extensiones\SeoModifierCompiler,
			'trim' => new \inc\extensiones\TrimModifierCompiler,
			default => null
		};

	}

}