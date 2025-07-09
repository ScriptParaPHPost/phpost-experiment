<?php

/**
 * Nuevo centro de instalación
 * 
 * @name install.php
 * @author PHPost Team
 * @copyright 2011-2025
 */

define('PHPOST_CORE_LOADED', TRUE);

# Muy importante
$FileConfig = __DIR__ . '/../storage/config.inc.php';
if(!file_exists($FileConfig)) {
	copy(__DIR__ . '/example.php', $FileConfig);
	chmod($FileConfig, 0666);
}

# Incluimos los archivos necesarios
require_once __DIR__ . '/../core/utils/Config.php';
require_once __DIR__ . '/../core/database/DB.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/componentes.php';

# Inicializamos las clases
$Config = new config($FileConfig);
$Utils = new Utils($Config->get('db'));
$Component = new Componentes;

$Utils->isInstalled();

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');
error_reporting(E_ALL ^ E_WARNING);

// Iniciamos la sesión.
session_name('INSTALLER');
session_start();

$version = "{$Config->get('app.name')} v{$Config->get('app.version')}";
// Config app
$SettingsDefault = [
	'slogan' => 'Script actualizado',
	'pkey' => '6LfFFiMdAAAAAAQjDafWXZ0FeyesKYjVm4DSUoao',
	'skey' => '6LfFFiMdAAAAAFIP4oNFLQx5Fo1FyorTzNps8ChE',
];

$step = (int)$Utils->sanitizer('step', INPUT_GET);
$next = true; // CONTINUAR
$message = '';

switch ($step) {
	case 0:
		$license = file_get_contents(__DIR__ . '/../LICENSE');
		$_SESSION['TERMS_ACCEPTED'] = $Utils->sanitizer('license');
		if($Utils->isMethodPost() && $_SESSION['TERMS_ACCEPTED']) {
			header("Location: ?step=1&license=ok");
			exit;
		}
	break;
	case 1:
		if(!$_SESSION['TERMS_ACCEPTED']) header("Location: ./index.php");
		$permisos = [
			'config'  => ['root' => '/../storage/config.inc.php'],
			'cache' 	 => ['root' => '/../storage/cache/'],
			'avatar'  => ['root' => '/../storage/avatar/'],
			'uploads' => ['root' => '/../storage/uploads/']
		];
		foreach($permisos as $key => $permiso) {
			if(!is_dir(__DIR__ . $permiso['root']) && $key !== 'config') {
				mkdir(__DIR__ . $permiso['root'], 0777);
			}
			$permisos[$key]['chmod'] = (int)substr(sprintf('%o', fileperms(__DIR__ . $permiso['root'])), -3);
			$permisos[$key]['status'] = 'green';
			if($key === 'config' && $permisos[$key]['chmod'] !== 666) {
				$permisos[$key]['status'] = 'red';
				$next = false;
			} elseif($key !== 'config' && $permisos[$key]['chmod'] !== 777) {
				$permisos[$key]['status'] = 'red';
				$next = false;
			}
		}
		if(($Utils->isMethodPost() && $_SESSION['TERMS_ACCEPTED']) || $next) {
			header("Location: ?step=" . ($next ? 2 : 1));
			exit;
		}
	break;
	case 2:
		if(!$_SESSION['TERMS_ACCEPTED']) header("Location: ./index.php");
		$db = $Utils->sanitizer(['hostname', 'username', 'password', 'database']);
		$next = false;
		if($Utils->isMethodPost()) {
			try {
				// Solo para saber si exite la tabla y si se puede conectar
				$isConn = new DB($db);
				$rendered = $Utils->renderConfigTemplate($FileConfig, $db);
				if ($rendered === false) {
					$next = false;
					throw new RuntimeException('No se pudo procesar el archivo de configuración.');
				}
				# Guardamos el nuevo contenido
				if(file_put_contents($FileConfig, $rendered) === false) {
					$next = false;
					throw new RuntimeException('No se pudo guardar la configuración.');
				}
				$fresh = true;
				require_once __DIR__ . '/../core/database/migrar.php';
				
				if ($next && $_SESSION['TERMS_ACCEPTED']) {
					header("Location: index.php?step=3");
					exit;
				} else {
					$message = 'Lo sentimos, pero ocurrió un problema. Inténtalo nuevamente; borra las tablas que se hayan guardado en tu base de datos: ' . $error;
				}	
			} catch (Exception $e) {
				$message = $e->getMessage();
				$next = false;
			}
		}
	break;
	case 3:
		if(!$_SESSION['TERMS_ACCEPTED']) header("Location: ./index.php");
		$site = $Utils->sanitizer(['titulo', 'slogan', 'email', 'url', 'pkey', 'skey']);

		if($Utils->isMethodPost()) {
			$errors = [];
			foreach ($site as $key => $value) {
				if (empty($value)) {
					$errors[] = "El campo '$key' es obligatorio.";
					$next = false;
				}
			}

			if (!empty($errors)) {
				$message = implode('<br>', $errors);
			}
			# Iniciamos la conexión con la base de datos
			try {
				// Solo para saber si exite la tabla y si se puede conectar
				$DB = new DB($Config->get('db'));

				# Obtenemos el contenido del archivo
				$filename = file_get_contents($FileConfig);
				$site['domain'] = str_replace(['https://','http://'], '', $site['url']);
				# Reemplazamos el contenido por el nuevo
				foreach($site as $name => $infoData) {
					$filename = str_replace('{{' . $name . '}}', $infoData, $filename);
				}
				# Guardamos el nuevo contenido
				file_put_contents($FileConfig, $filename);

				# Verificamos la existencias de temas instalados
				$temas = $DB->select('w_temas', 'tid', ['tid' => 1]);
				if(count($temas) === 0) {
					$pathName = 'default';
					$DB->insert('w_temas', [
						't_name' => 'PHPostV4', 
						't_url' => "{$site['url']}/views/$pathName", 
						't_path' => $pathName, 
						't_copy' => 'Miguel92', 
						'tid' => 1
					]);
				}

				# Actualizamos la categoría
				$DB->update('p_categorias', [
					'c_nombre' => $site['titulo'],
					'c_seo' => $Utils->slugly($site['titulo']),
				], ['cid' => 30]);

				# Actualizando información del sitio
				$DB->update('w_config_general', [
					'titulo' => $site['titulo'],
					'slogan' => $site['slogan'],
					'url' => $site['url'],
					'email' => $site['email']
				], ['tscript_id' => 1]);

				# Actualizando información para el reCaptcha y Versión
				$DB->update('w_config_misc', [
					'pkey' => $site['pkey'],
					'skey' => $site['skey'],
					'version' => $version,
					'version_code' => $Utils->slugly($version, '_')
				], ['tscript_id' => 1]);

				if ($_SESSION['TERMS_ACCEPTED'] && $next) {
					header("Location: index.php?step=4");
				} else {
					$message = $error;
				}
			} catch (Exception $e) {
				$message = $e->getMessage();
			}
		}	
	break;
	case 4:
		if(!$_SESSION['TERMS_ACCEPTED']) header("Location: ./index.php");
		$user = $Utils->sanitizer(['username', 'password', 'email']);
		if($Utils->isMethodPost()) {
			if(in_array('', $user, true)) {
				$message = 'Todos los campos deben estar completos';
				$next = false;
			}
			if (!ctype_alnum($user['username'])) {
				$message = 'Introduzca un nombre de usuario alfanum&eacute;rico';
				$next = false;
			}
			if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
				$message = 'Introduzca un email correcto.';
				$next = false;
			}
			# Creando la contraseña del usuario
			require_once __DIR__ . '/../core/utils/passwordManager.php';
			$pm = new PasswordManager(12);
			if ($message = $pm->validatePassword($user['password'])) {
				$next = false;
			}
			if($_SESSION['TERMS_ACCEPTED'] && $next) {
				$hash = $pm->hash($user['password']);
				$fecha = time();
				# Iniciamos la conexión con la base de datos
				try {

					if ($next) {
						// Solo para saber si exite la tabla y si se puede conectar
						$DB = new DB($Config->get('db'));

						$uid = (int)$DB->insert('u_miembros', [
							'user_name' => $user['username'], 
							'user_password' => $hash, 
							'user_email' => $user['email'], 
							'user_rango' => 1,
							'user_registro' => $fecha,
							'user_puntosxdar' => 50,
							'user_activo' => 1
						]);
				
						$path_user_uid = "UID_{$uid}";
				      $directory = __DIR__ . '/../storage/avatar/' . $path_user_uid;
				      // Crear carpeta si no existe
				      mkdir($directory, 0777, true);
				      chmod($directory, 0777);
				      
				      // Generar avatar desde API externa
				      $download_file = "https://ui-avatars.com/api/?name=" . urlencode($user['username']) . "&background=random&size=180&font-size=0.60&length=2&format=webp";
				      copy($download_file, $directory . '/default.webp');

				      # Insertamos datos necesarios
				      $DB->insert('u_perfil', ['user_id' => $uid]);
				      $DB->insert('u_portal', ['user_id' => $uid]);
						# Actualizamos el tiempo de fundación del sitio
						$DB->update('w_stats', [
							'stats_time_foundation' => $fecha,
							'stats_time_upgrade' => $fecha
						], ['stats_no' => 1]);
						// DAMOS BIENVENIDA POR CORREO
						$Utils->enviar([
							'email' => $user['email'],
							'placeholder' => [
						      'usuario' => $user['username'],
						      'contrasena' => $user['password']
						   ],
						   'plantilla' => 'bienvenida',
						   'asunto' => 'Su comunidad ya puede ser usada', 
						   'headers' => $Config->get('mail')
						]);
						header("Location: index.php?step=5&uid=" . $uid);
					} else {
						$message = $error;
					}
				} catch (Exception $e) {
					$message = $e->getMessage();
				}
			}
		}
	break;
	case 5:
		if(!$_SESSION['TERMS_ACCEPTED']) header("Location: ./index.php");
		if($_SESSION['TERMS_ACCEPTED'] && $next) {

			# Iniciamos la conexión con la base de datos
			try {
				// Solo para saber si exite la tabla y si se puede conectar
				$DB = new DB($Config->get('db'));
				$data = $DB->select('w_config_general', ['titulo', 'slogan', 'url'], ['tscript_id' => 1]);

				if($Utils->isMethodPost()) {
					// CONSULTA
					$uid = (int)filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT) ?? 0;
					$user = $DB->select('u_miembros', 'user_name', ['user_id' => $uid]);
					// ESTADISTICAS
					$code = [
						'title' => $data['titulo'], 
						'slogan' => $data['slogan'], 
						'url' => $data['url'], 
						'version' => $Utils->slugly($version, '_'), 
						'admin' => $user['user_name'], 
						'id' => $uid
					];
					$key = base64_encode(serialize($code));
					# Creamos los archivos
					$Utils->create_file('.key', $key);
					$Utils->create_file('.lock', 'Ha sido instalado y configurado correctamente.');
					$Utils->create_file('.version', $Config->get('app.version'));
					session_unset();
					session_destroy();
		         header('Location: ../');
				}
			} catch (Exception $e) {
				$message = $e->getMessage();
			}
		}
	break;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="PHPost" />
<link href="<?= $Utils->getUrl('/../Cover.png') ?>" rel="icon" type="image/png" sizes="64x64">
<title>Instalaci&oacute;n de <?= $Config->get('app.name') ?></title>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<style type="text/tailwindcss">
	.pattern {
	   inset: 0;
	   z-index: 0;
	   background-image: 
	     linear-gradient(to right, #e2e8f0 1px, transparent 1px),
	     linear-gradient(to bottom, #e2e8f0 1px, transparent 1px);
	   background-size: 20px 30px;
	   -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 0%, #000 60%, transparent 100%);
	   mask-image: radial-gradient(ellipse 70% 60% at 50% 0%, #000 60%, transparent 100%);
	}
</style>
</head>
<body class="min-h-screen w-full bg-[#f8fafc] relative">

	<div class="pattern fixed"></div>

	<main class="w-3xl min-h-screen relative m-auto">

		<header class="flex justify-center items-center relative gap-3 py-8">
			<img class="aspect-square rounded-full" width="50" height="50" src="<?= $Utils->getUrl('/../Cover.png') ?>" />
			<h1 class="text-3xl font-bold"><?= $Config->get('app.name') ?></h1>
		</header>

		<section>
			<form method="POST">
				<fieldset>
					<!-- Inicio -->
					<?php if($step === 0):

						echo $Component->legend('Licencia');
						echo $Component->text("Para utilizar <strong>{$Config->get('app.name')}</strong> debes estar de acuerdo con nuestra licencia de uso."); ?>
				
						<textarea class="w-full max-h-max p-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none shadow-sm placeholder-gray-400 text-gray-900 bg-white [field-sizing:content]" rows="15"><?= htmlspecialchars($license) ?></textarea>
						<input type="hidden" name="license" value="true">
						<?= $Component->button("Aceptar y Continuar");

					elseif($step === 1): 

						echo $Component->legend('Permisos de escritura');
						echo $Component->text("Los siguientes archivos y directorios requieren de permisos especiales, debes cambiarlos desde tu cliente FTP, los archivos deben tener permiso <strong>666</strong> y los direcorios <strong>777</strong>");

						foreach($permisos as $key => $permiso):
							$texto = ($permiso['status'] === 'green') ? 'Permiso correcto' : 'Dar permiso';
							echo $Component->field([
								'aditional' => "<span class=\"font-bold rounded-md px-4 py-2 text-{$permiso['status']}-800 bg-{$permiso['status']}-200/25\" id=\"$key\">$texto</span>",
								'for' => $key,
								'label' => ucfirst(basename($permiso['root'])),
								'small' => $permiso['root']
							]);
						endforeach; 

						echo $Component->button($next ? 'Continuar &raquo;' : 'Volver a verificar');

					elseif($step === 2): 

						echo $Component->legend('Base de datos');
						echo $Component->text("Ingresa tus datos de conexi&oacute;n a la base de datos.");
						echo $Component->message($message);

						echo $Component->field([
							'input' => ['type' => 'text','placeholder' => 'localhost','value' => $db['hostname'],'required' => true],
							'for' => 'hostname',
							'label' => 'Servidor',
							'small' => 'Donde est&aacute; la base de datos, ej: <strong>localhost</strong>'
						]);

						echo $Component->field([
							'input' => ['type' => 'text','placeholder' => 'root','value' => $db['username'],'required' => true],
							'for' => 'username',
							'label' => 'Usuario',
							'small' => 'El usuario de tu base de datos.'
						]);

						echo $Component->field([
							'input' => ['type' => 'password','placeholder' => '******','value' => $db['password']],
							'for' => 'password',
							'label' => 'Contrase&ntilde;a',
							'small' => 'Para acceder a la base de datos.'
						]); 

						echo $Component->field([
							'input' => ['type' => 'text','placeholder' => 'mydb','value' => $db['database'],'required' => true],
							'for' => 'database',
							'label' => 'Base de datos',
							'small' => 'Nombre de la base de datos para tu web.'
						]); 

						echo $Component->button('Continuar &raquo;');

					elseif($step === 3): 

						echo $Component->legend('Datos del sitio');
						echo $Component->message($message);

						echo $Component->field([
							'input' => [
								'type' => 'text',
								'placeholder' => $Config->get('app.name'),
								'value' => $site['titulo'],
								'required' => true
							],
							'for' => 'titulo',
							'label' => 'Nombre',
							'small' => 'El t&iacute;tulo de tu web'
						]); 

						echo $Component->field([
							'input' => [
								'type' => 'text',
								'placeholder' => $SettingsDefault['slogan'],
								'value' => $site['slogan'],
								'required' => true
							],
							'for' => 'slogan',
							'label' => 'Lema / Slogan',
							'small' => 'Ej: Inteligencia recargada'
						]); 

						echo $Component->field([
							'input' => [
								'type' => 'url',
								'placeholder' => $Utils->getUrl(),
								'value' => $site['url'] ?? $Utils->getUrl(),
								'required' => true
							],
							'for' => 'url',
							'label' => 'Direcci&oacute;n',
							'small' => 'Ingresa la url donde  est&aacute; alojada tu web, sin la &uacute;ltima diagonal <strong>/</strong> </small>'
						]); 

						echo $Component->field([
							'input' => [
								'type' => 'email',
								'placeholder' => 'noreply@example.com',
								'value' => $site['email'],
								'required' => true
							],
							'for' => 'email',
							'label' => 'Email',
							'small' => 'Email de la web o del administrador'
						]); 

						echo $Component->legend('Datos de reCAPTCHA');
						echo $Component->text("Obtén tu clave desde <a href=\"https://www.google.com/recaptcha/admin\" target=\"_blank\"><strong>www.google.com/recaptcha/admin</strong></a>");
						echo $Component->field([
							'input' => [
								'type' => 'text',
								'placeholder' => $SettingsDefault['pkey'],
								'value' => $site['pkey'],
								'required' => true
							],
							'for' => 'pkey',
							'label' => 'Clave pública del sitio'
						]); 
						echo $Component->field([
							'input' => [
								'type' => 'text',
								'placeholder' => $SettingsDefault['skey'],
								'value' => $site['skey'],
								'required' => true
							],
							'for' => 'skey',
							'label' => 'Clave secreta'
						]); 
						echo $Component->button('Continuar &raquo;');

					elseif($step === 4): 

						echo $Component->legend('Datos del administrador');
						echo $Component->text("Ingresa tus datos de usuario, m&aacute;s adelante debes editar tu cuenta para ingresar datos como, fecha de nacimiento, lugar de residencia, etc.");
						echo $Component->message($message);

						echo $Component->field([
							'input' => [
								'type' => 'text',
								'placeholder' => 'JohnDoe',
								'value' => $user['username'],
								'required' => true
							],
							'for' => 'username',
							'label' => 'Nombre de usuario'
						]);
						echo $Component->field([
							'input' => [
								'type' => 'email',
								'placeholder' => 'jhondoe@some.com',
								'value' => $user['email'],
								'required' => true
							],
							'for' => 'email',
							'label' => 'Email',
							'small' => 'Ingresa tu direcci&oacute;n de email'
						]);
						echo $Component->field([
							'input' => [
								'type' => 'text',
								'placeholder' => '#myPassLarge2025',
								'value' => $user['password'],
								'required' => true
							],
							'for' => 'password',
							'label' => 'Contrase&ntilde;a',
							'data-password' => 'true',
							'optional' => "<ul id=\"rules\" class=\"text-xs mt-2\">
								<li id=\"symbol\" class=\"invalid\">🔒 Contiene al menos un símbolo</li>
								<li id=\"uppercase\" class=\"invalid\">🔡 Contiene al menos una mayúscula</li>
								<li id=\"number\" class=\"invalid\">🔢 Contiene al menos un número</li>
								<li id=\"length\" class=\"invalid\">📏 Mínimo 8 caracteres</li>
							</ul>"
						]);
						echo $Component->button('Continuar &raquo;');
						echo $Component->script($Utils->getUrl('/install/password_strong.js'));

					elseif($step === 5): 

						echo $Component->legend("💚 ¡Gracias por instalar {$Config->get('app.name')}!");
						echo $Component->text("Tu nueva comunidad <strong>Link Sharing System</strong> ya está lista para comenzar a compartir."); 
						echo $Component->text("Iniciá sesión con tus datos y explorá este espacio que sigue vivo gracias al esfuerzo de quienes creemos que aún vale la pena.<br>Esta actualización fue creada con dedicación, cariño y la firme decisión de no dejar morir algo que aún tiene mucho por dar.<br>No te olvides de <a href=\"http://github.com/isidromlc/PHPost\" target=\"_blank\" style=\"font-weight: 600;\">visitarnos</a> para estar al tanto de futuras mejoras, y si encontrás algún error, ¡avisanos! Así seguimos creciendo, entre todos."); 
						echo $Component->text("¡Bienvenido de nuevo a la comunidad que nunca se rinde! ✊"); 
						echo $Component->button('Finalizar instalación');
						echo $Component->message("Ingresa a tu FTP y borra la carpeta <strong>".basename(getcwd())."</strong> antes de usar el script."); 

					endif; ?>
					<!-- Fin -->
				</fieldset>
			</form>
		</section>
		<footer class="flex justify-center items-center flex-col py-4">
			<p>Powered by <a class="font-bold text-blue-800" href="https://github.com/joelmiguelvalente" target="_blank">Miguel92</a></p>
			<small class="text-sm">Versión actual: v<strong class="text-blue-800"><?= $Config->get('app.version') ?></strong></small>
		</footer>
	</main>
	
</body>
</html>