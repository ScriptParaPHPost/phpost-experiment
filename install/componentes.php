<?php

declare(strict_types=1);

/**
 * PHPost 2025
 * Clase Componentes
 *
 * Genera elementos reutilizables en HTML con clases Tailwind para el instalador u otras interfaces.
 * Incluye leyendas, textos, botones, mensajes, scripts y campos de formularios personalizados.
 * 
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

class Componentes {
	
	/**
	 * Genera un título con estilo tipo leyenda.
	 *
	 * @param string $string Texto del encabezado.
	 * @return string HTML generado para el legend.
	 */
	public function legend(string $string = 'LEGEND'): string {
		return <<<HTML
			<legend class="font-bold text-2xl text-blue-500">{$string}</legend>
		HTML;
	}

	/**
	 * Genera un párrafo de texto informativo.
	 *
	 * @param string $string Contenido del texto.
	 * @return string HTML generado para el párrafo.
	 */
	public function text(string $string = 'TEXT'): string {
		return <<<HTML
			<p class="my-3 text-lg">{$string}</p>
		HTML;
	}

	/**
	 * Genera un botón de formulario estilizado.
	 *
	 * @param string $string Texto del botón.
	 * @param string $type Tipo de botón (submit, button, etc).
	 * @return string HTML del botón generado.
	 */
	public function button(string $string = 'BUTTON', string $type = 'submit'): string {
		return <<<HTML
		<div class="buttons py-4 text-center">
			<input type="{$type}" value="{$string}" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition"/>
		</div>
		HTML;
	}

	/**
	 * Muestra un mensaje de error en un contenedor estilizado.
	 *
	 * @param string $message Mensaje a mostrar.
	 * @return string HTML del mensaje (vacío si no hay contenido).
	 */
	public function message(string $message): string {
		if (empty($message)) return '';
		return <<<HTML
			<div class="backdrop-blur-xs bg-red-100/25 text-red-800 p-4 rounded shadow-red-100">{$message}</div>
		HTML;
	}

	/**
	 * Inserta una etiqueta <script> para cargar un archivo JS.
	 *
	 * @param string $file Ruta al archivo JavaScript.
	 * @return string HTML del script.
	 */
	public function script(string $file): string {
		return "<script src=\"{$file}\"></script>";
	}

	/**
	 * Genera un campo completo con etiqueta, input y descripciones.
	 *
	 * @param array $data {
	 *    @type string   $for         Nombre del campo (usado en id y name).
	 *    @type string   $label       Texto de la etiqueta.
	 *    @type string   $small       Texto pequeño informativo (opcional).
	 *    @type array    $input       Atributos del input (type, placeholder, value, required).
	 *    @type string   $aditional   HTML personalizado a mostrar en lugar del input (opcional).
	 *    @type string   $optional    HTML adicional como reglas o validadores visuales (opcional).
	 * }
	 *
	 * @return string HTML del campo renderizado.
	 */
	public function field(array $data): string {
		$id = $data['for'];
		$label = $this->label($data['label'], $id);
		$small = !empty($data['small']) ? $this->small($data['small']) : '';
		$optional = $data['optional'] ?? '';

		$content = $data['aditional'] ?? $this->input($data['input'], $id);
		$style = 'justify-start items-center';

		if (!empty($optional)) {
			$content = "<div id=\"strength\" class=\"strength absolute right-0 top-12 text-xs\"></div>{$content}";
			$style = 'flex-col items-start';
		}

		return <<<HTML
		<dl class="flex py-4">
			<dt class="w-2/5">{$label}{$small}</dt>
			<dd class="w-3/5 flex {$style} relative">{$content}{$optional}</dd>
		</dl>
		HTML;
	}

	/**
	 * Genera la etiqueta <label> para un campo.
	 *
	 * @param string $text Texto de la etiqueta.
	 * @param string $for  ID al que se asocia.
	 * @return string HTML del label.
	 */
	private function label(string $text, string $for): string {
		return <<<HTML
			<label for="{$for}" class="block font-bold">{$text}</label>
		HTML;
	}

	/**
	 * Muestra un texto pequeño tipo ayuda.
	 *
	 * @param string $text Texto del small.
	 * @return string HTML del small.
	 */
	private function small(string $text): string {
		return <<<HTML
			<small class="text-xs font-italic">{$text}</small>
		HTML;
	 }

	/**
	 * Genera un campo input con clases Tailwind.
	 *
	 * @param array  $attr Atributos del input (type, placeholder, value, required).
	 * @param string $id   Nombre/id del input.
	 * @return string HTML del input.
	 */
	private function input(array $attr, string $id): string {
		$type = $attr['type'] ?? 'text';
		$placeholder = $attr['placeholder'] ?? '';
		$value = $attr['value'] ?? '';
		$required = !empty($attr['required']) ? 'required' : '';
		return <<<HTML
			<input type="{$type}" id="{$id}" name="{$id}" value="{$value}" placeholder="{$placeholder}" autocomplete="off" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 placeholder-gray-400 bg-white"{$required}/>
		HTML;
	}
}