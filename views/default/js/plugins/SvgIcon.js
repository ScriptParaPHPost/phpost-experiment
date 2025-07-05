export class SvgIconClass extends HTMLElement {
	constructor() {
		super();
		this.attachShadow({ mode: 'open' });
	}

	async connectedCallback() {
		const { theme: apiUrl } = AppData;
		const name = this.getAttribute('name');
		const className = this.getAttribute('class') || '';

		if (!name) {
			console.warn('svg-icon: Falta el atributo "name".');
			return;
		}

		try {
			const response = await fetch(`${apiUrl}/images/svg/${name}.svg`);
			if (!response.ok) throw new Error('No se pudo cargar el SVG');

			let svgText = await response.text();

			// ✅ Opcional: modificar el SVG inyectado para que herede `currentColor`
			svgText = svgText.replace(
				/<svg([^>]+)>/,
				`<svg$1 fill="currentColor">`
			);

			this.shadowRoot.innerHTML = `
				<style>
					:host {
						display: inline-block;
						width: 1.5em;
						height: 1.5em;
						color: inherit;
					}
					svg {
						width: 100%;
						height: 100%;
						display: block;
					}
				</style>
				<div class="${className}">${svgText}</div>
			`;
		} catch (err) {
			console.error(`Error al cargar el SVG "${name}":`, err);
			this.shadowRoot.innerHTML = `<span style="color:red;">❌ SVG no encontrado</span>`;
		}
	}
}
// Exportar una función que registre el componente
export function SvgIcon() {
	if (!customElements.get('svg-icon')) {
		customElements.define('svg-icon', SvgIconClass);
	}
}