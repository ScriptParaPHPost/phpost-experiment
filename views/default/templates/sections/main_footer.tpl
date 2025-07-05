			</section>
		</main>
		<footer id="site-footer" class="p-6" aria-label="Pie de página">
			<div class="container">
				<div class="footer-content flex justify-between items-center lg:flex-wrap sm:flex-col md:flex-row md:text-center">
					<div class="footer-links">
						<div class="footer-top flex justify-start gap-4">
							<a class="no-underline hover:underline" title="Enlace del sitio - Ayuda" href="{$tsRoutes.url}/pages/ayuda/">Ayuda</a>
							<a class="no-underline hover:underline" title="Enlace del sitio - Chat" href="{$tsRoutes.url}/pages/chat/">Chat</a>
							<a class="no-underline hover:underline" title="Enlace del sitio - Contacto" href="{$tsRoutes.url}/pages/contacto/">Contacto</a>
							<a class="no-underline hover:underline" title="Enlace del sitio - Protocolo" href="{$tsRoutes.url}/pages/protocolo/">Protocolo</a>
						</div>
						<div class="footer-bottom flex justify-start gap-4">
							<a class="no-underline hover:underline" title="Enlace del sitio - Términos y condiciones" href="{$tsRoutes.url}/pages/terminos-y-condiciones/">Términos y condiciones</a>
							<a class="no-underline hover:underline" title="Enlace del sitio - Privacidad de datos" href="{$tsRoutes.url}/pages/privacidad/">Privacidad de datos</a>
							<a class="no-underline hover:underline" title="Enlace del sitio - Report Abuse - DMCA" href="{$tsRoutes.url}/pages/dmca/">Report Abuse - DMCA</a>
						</div>
					</div>

					<div class="footer-meta md:text-right sm:text-center sm:mt-4 md:mt-0">
						<p>
						  <a href="{$tsRoutes.url}/" title="Enlace del sitio {$tsConfig.titulo}">{$tsConfig.titulo}</a> &copy; {$smarty.now|date_format:"%Y"}
						</p>
						<p class="text-xs">
							<span class="block">Desarrollado por <a href="https://github.com/joelmiguelvalente" title="Github del desarrollador" target="_blank" rel="noopener">Miguel92</a></span>
							<span>Versión: <a href="https://github.com/isidromlc/PHPost" title="Github del repositorio original" target="_blank" rel="noopener">v{$tsAppVersion}</a></span>
						</p>
					</div>
				</div>
			</div>
		</footer>

	</div>

</body>
</html>