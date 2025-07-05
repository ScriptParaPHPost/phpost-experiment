<script>
 window.AppData = {
	domain: '{$tsRoutes.domain|escape:'javascript'}',
	id: {
		foto: {$tsFoto.foto_id|default:0|escape:'javascript'},
		post: {$tsPost.post_id|default:0|escape:'javascript'},
		user: {$tsUser->uid|default:0|escape:'javascript'}
	},
	slogan: '{$tsConfig.slogan|escape:'javascript'}',
	system: {
		action: '{$tsAction|escape:'javascript'}',
		messages: {$tsMPs|default:0|escape:'javascript'},
		notifications: {$tsNots|default:0|escape:'javascript'},
		page: '{$tsPage|escape:'javascript'}'
	},
	theme: '{$tsRoutes.tema|escape:'javascript'}',
	title: '{$tsConfig.titulo|escape:'javascript'}',
	url: '{$tsRoutes.url|escape:'javascript'}'
};
</script>