export const lazyload = (() => {
	const images = document.querySelectorAll('[data-src]');
	
	if (!('IntersectionObserver' in window)) {
		// Fallback si el navegador no lo soporta
		images.forEach(img => img.src = img.dataset.src);
		return;
	}

	const observer = new IntersectionObserver((entries, observerInstance) => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				const img = entry.target;
				img.src = img.dataset.src;
				img.removeAttribute('data-src');
				img.classList.remove('lazy');
				observerInstance.unobserve(img);
			}
		});
	}, {
		rootMargin: "0px 0px 200px 0px", // carga un poco antes de que se vea
		threshold: 0.04
	});

	images.forEach(img => observer.observe(img));

	return {
		refresh() {
		 	// Para cargar nuevas imágenes si las agregás después dinámicamente
		 	document.querySelectorAll('[data-src]').forEach(img => observer.observe(img));
		}
	};
})();
