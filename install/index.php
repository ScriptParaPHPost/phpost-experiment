<?php

/**
 * Nuevo centro de instalación
 * 
 * @name install.php
 * @author PHPost Team
 * @copyright 2011-2025
 */

if(file_exists(__DIR__.'/../.lock') && file_exists(__DIR__.'/../.key')) header("Location: ../");

define('PHPOST_CORE_LOADED', TRUE);

require_once __DIR__ . '/../inc/utils/Avatar.php';
$Avatar = new Avatar;

require_once __DIR__ . '/../inc/utils/Globals.php';
$Globals = new Globals;

require_once __DIR__ . '/utils.php';
$utils = new Utils;

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');
error_reporting(E_ALL ^ E_WARNING);

// Iniciamos la sesión.
session_name('INSTALLER');
session_start();

// Config app
$config = [
	'script' => [
		'name' => 'PHPost v4',
		'slogan' => 'Script actualizado',
		'version' => 'PHPost v' . $Globals->getVersion('version'),
		'version_code' => 'phpost_v' . $Globals->getVersion('code')
	],
	'pkey' => '6LfFFiMdAAAAAAQjDafWXZ0FeyesKYjVm4DSUoao',
	'skey' => '6LfFFiMdAAAAAFIP4oNFLQx5Fo1FyorTzNps8ChE',
];

$step = (int)filter_input(INPUT_GET, 'step', FILTER_SANITIZE_NUMBER_INT) ?? 0;
$next = true; // CONTINUAR
$message = '';

switch ($step) {
	case 0:
		$license = file_get_contents(__DIR__ . '/../LICENSE');
		$_SESSION['TERMS_ACCEPTED'] = filter_input(INPUT_POST, 'license', FILTER_VALIDATE_BOOLEAN);
		if($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['TERMS_ACCEPTED']) {
			header("Location: ?step=1&license=ok");
		}
	break;
	case 1:
		if(!$_SESSION['TERMS_ACCEPTED']) header("Location: ./index.php");
		$permisos = [
			'config' => [
				'path' => 'config.inc.php',
				'root' => '/../config.inc.php'
			],
			'cache' => [
				'path' => 'cache',
				'root' => '/../files/cache/'
			],
			'avatar' => [
				'path' => 'avatar',
				'root' => '/../files/avatar/'
			],
			'uploads' => [
				'path' => 'uploads',
				'root' => '/../files/uploads/'
			]
		];
		foreach($permisos as $key => $permiso) {
			if(!file_exists(__DIR__ . $permiso['root']) && $key === 'config') {
				copy(__DIR__ . '/example.php', __DIR__ . $permiso['root']);
				chmod(__DIR__ . $permiso['root'], 0666);
			} elseif(!is_dir(__DIR__ . $permiso['root']) && $key !== 'config') {
				mkdir(__DIR__ . $permiso['root'], 0777);
			}
			$permisos[$key]['chmod'] = (int)substr(sprintf('%o', fileperms(__DIR__ . $permiso['root'])), -3);
			$permisos[$key]['status'] = 'success';
			if($key === 'config' && $permisos[$key]['chmod'] !== 666) {
				$permisos[$key]['status'] = 'danger';
				$next = false;
			} elseif($key !== 'config' && $permisos[$key]['chmod'] !== 777) {
				$permisos[$key]['status'] = 'danger';
				$next = false;
			}
		}
		
		if(($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['TERMS_ACCEPTED']) || $next) {
			header("Location: ?step=" . ($next ? 2 : 1));
		}
	break;
	case 2:
		if(!$_SESSION['TERMS_ACCEPTED']) header("Location: ./index.php");

		$db = [
			'hostname' => filter_input(INPUT_POST, 'dbhost', FILTER_UNSAFE_RAW) ?? '',
			'username' => filter_input(INPUT_POST, 'dbuser', FILTER_UNSAFE_RAW) ?? '',
			'password' => filter_input(INPUT_POST, 'dbpass', FILTER_UNSAFE_RAW) ?? '',
			'database' => filter_input(INPUT_POST, 'dbname', FILTER_UNSAFE_RAW) ?? ''
		];
		$next = false;

		if($_SERVER['REQUEST_METHOD'] === 'POST') {

			try {
				$mysqli = $utils->dbConnect($db);

				# Obtenemos el contenido del archivo
				$filename = file_get_contents(__DIR__ . '/../config.inc.php');
				# Reemplazamos el contenido por el nuevo
				$replace = str_replace(['dbhost', 'dbuser', 'dbpass', 'dbname'], $db, $filename);
				# Guardamos el nuevo contenido
				file_put_contents(__DIR__ . '/../config.inc.php', $replace);

				# Antes comprobamos que no este instalado
				if($results = $mysqli->query("SHOW TABLES")) {
					# Eliminamos todas las tablas existentes
					while ($row = $results->fetch_row()) $mysqli->query("DROP TABLE {$row[0]}");
					$results->close();
				}

				# Añadimos la base de datos
				require_once __DIR__ . '/../inc/migration/database.php';
				$error = '';
				foreach($phpost_mysqli as $table => $sql) {
					if($mysqli->query($sql)) {
						$execute[$table] = 1;
					} else {
						$execute[$table] = 0;
						$error .= '<br/>' . $mysqli->error;
					}
				}
				
				if (in_array(1, $execute, true) && $_SESSION['TERMS_ACCEPTED']) {
					header("Location: index.php?step=3");
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
		$site = [
			'titulo' => filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS) ?? '',
			'slogan' => filter_input(INPUT_POST, 'slogan', FILTER_SANITIZE_SPECIAL_CHARS) ?? '',
			'email' => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?? '',
			'url' => filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL) ?? $utils->getUrl(),
			'pkey' => filter_input(INPUT_POST, 'pkey', FILTER_UNSAFE_RAW) ?? '',
			'skey' => filter_input(INPUT_POST, 'skey', FILTER_UNSAFE_RAW) ?? ''
		];

		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			if(in_array('', $site, true)) {
				$message = 'Todos los campos deben estar completos';
			}
			# Cargamos el archivo de conexion
			$req = require_once __DIR__ . '/../config.inc.php';
			
			# Iniciamos la conexión con la base de datos
			try {
				$mysqli = $utils->dbConnect($req['db']);
				# Verificamos si hay un theme instalado
				$result = $mysqli->query("SELECT tid FROM w_temas WHERE tid = 1");
				if (!$result || $result->num_rows === 0) {
					$mysqli->query("INSERT INTO `w_temas` (`tid`, `t_name`, `t_url`, `t_path`, `t_copy`) VALUES (1, 'PHPostV4', '{$site['url']}/views/default', 'default', 'Miguel92')");
				}

				# Anteriormente se preguntaba si existia un usuario, ya no es necesario
				$setear = $Globals->slugly($site['titulo']);

				// UPDATE
				$updates = [
					"UPDATE p_categorias SET c_nombre = '{$site['titulo']}', c_seo = '$setear' WHERE cid = 30",
					"UPDATE w_config_general SET titulo = '{$site['titulo']}', slogan = '{$site['slogan']}', url = '{$site['url']}', email = '{$site['email']}'",
					"UPDATE w_config_misc SET pkey = '{$site['pkey']}', skey = '{$site['skey']}', version = '{$config['script']['version']}', version_code = '{$config['script']['version_code']}'"
				];
				$error = '';
				foreach($updates as $k => $update) {
					if($mysqli->query($update)) {
						$execute[$k] = 1;
					} else {
						$execute[$k] = 0;
						$error .= '<br/>' . $mysqli->error;
					}
				}

				if (in_array(1, $execute, true) && $_SESSION['TERMS_ACCEPTED'] && $next) {
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
		$user = [
			'username' => filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS) ?? '',
			'password' => filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS) ?? '',
			'email' => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?? ''
		];

		if($_SERVER['REQUEST_METHOD'] === 'POST') {

			require_once __DIR__ . '/../inc/utils/passwordManager.php';
			$pm = new PasswordManager(12); 

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
			if ($message = $pm->validatePassword($user['password'])) {
				$next = false;
			}
			if($_SESSION['TERMS_ACCEPTED'] && $next) {

				$hash = $pm->hash($user['password']);
				$fecha = time();
				# Cargamos el archivo de conexion
				$req = require_once __DIR__ . '/../config.inc.php';
				
				# Iniciamos la conexión con la base de datos
				try {

					if ($next) {
						$mysqli = $utils->dbConnect($req['db']);
						$mysqli->query("INSERT INTO `u_miembros` (`user_name`, `user_password`, `user_email`, `user_rango`, `user_registro`, `user_puntosxdar`, `user_activo`) VALUES ('{$user['username']}', '$hash', '{$user['email']}', 1, $fecha, 50, 1)");
						$uid = (int)$mysqli->insert_id;
						$Avatar->get((int)$uid, $user['username']);
						$mysqli->query("INSERT INTO u_perfil (user_id) VALUES ($uid)");
						$mysqli->query("INSERT INTO u_portal (user_id) VALUES ($uid)");
						// UPDATE
						$mysqli->query("UPDATE w_stats SET stats_time_foundation = $fecha, stats_time_upgrade = $fecha WHERE stats_no = 1");
						// DAMOS BIENVENIDA POR CORREO
						$Globals->enviar($user['email'],
						   [
						      'usuario' => $user['username'],
						      'contrasena' => $user['password']
						   ],
						   'bienvenida',
						   'Su comunidad ya puede ser usada'
						);
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

			# Cargamos el archivo de conexion
			$req = require_once __DIR__ . '/../config.inc.php';
			
			# Iniciamos la conexión con la base de datos
			try {
				$mysqli = $utils->dbConnect($req['db']);
				$data = $mysqli->query("SELECT titulo, slogan, url FROM w_config_general WHERE tscript_id = 1")->fetch_assoc();
				$version = $mysqli->query("SELECT version_code FROM w_config_misc WHERE tscript_id = 1")->fetch_assoc();
				if($_SERVER['REQUEST_METHOD'] === 'POST') {
					// CONSULTA
					$uid = (int)filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT) ?? 0;
					$user = $mysqli->query("SELECT user_name FROM u_miembros WHERE user_id = $uid")->fetch_assoc();
					// ESTADISTICAS
					$code = [
						'title' => $data['titulo'], 
						'slogan' => $data['slogan'], 
						'url' => $data['url'], 
						'version' => $version['version_code'], 
						'admin' => $user['user_name'], 
						'id' => $uid
					];
					$key = base64_encode(serialize($code));
					// Abrir el archivo en modo de escritura ("w")
		         $mykey = fopen(__DIR__ . '/../storage/.key', "w");
		         // Escribir los datos en el archivo
		         fwrite($mykey, $key);
		         // Cerrar el archivo
		         fclose($mykey);

					// Abrir el archivo en modo de escritura ("w")
		         $handle = fopen(__DIR__ . '/../storage/.lock', "w");
		         // Escribir los datos en el archivo
		         fwrite($handle, 'Ha sido instalado y configurado correctamente.');
		         // Cerrar el archivo
		         fclose($handle);
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
<title>Instalaci&oacute;n de <?= $config['script']['name'] ?></title>
<link href="<?= $utils->getUrl('/install/estilo.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?= $utils->getUrl('/../Cover.png') ?>" rel="icon" type="image/png" sizes="64x64">
</head>
<body>

	<main>
		<header>
			<img src="<?= $utils->getUrl('/../Cover.png') ?>" />
			<h1>Programa de instalaci&oacute;n: <strong><?= $config['script']['name'] ?></strong></h1>
		</header>
		<section>
			<form method="POST">
				<fieldset>
					<!-- Inicio -->
					<?php if($step === 0): ?>

						<legend>Licencia</legend>
						<p class="lead">Para utilizar <?= $config['script']['name'] ?> debes estar de acuerdo con nuestra licencia de uso.</p>
						<textarea rows="15"><?= $license ?></textarea>
						<div class="buttons">
							<input type="hidden" name="license" value="true">
							<input type="submit" value="Acepto"/>
						</div>

					<?php elseif($step === 1): ?>

						<legend>Permisos de escritura</legend>
						<p class="lead">Los siguientes archivos y directorios requieren de permisos especiales, debes cambiarlos desde tu cliente FTP, los archivos deben tener permiso <strong>666</strong> y los direcorios <strong>777</strong></p>

						<?php foreach($permisos as $key => $permiso): ?>
							<dl>
								<dt>
									<label for="<?= $key ?>"><?= ucfirst($permiso['path']) ?></label>
									<small><?= $permiso['root'] ?></small>
								</dt>
								<dd><span class="status <?= $permiso['status'] ?>" id="<?= $key ?>"><?= ($permiso['status'] === 'success' ? 'Permiso correcto' : 'Dar permiso') ?></span></dd>
							</dl>
						<?php endforeach; ?>

						<div class="buttons">
							<input type="submit" value="<?= $next ? 'Continuar &raquo;' : 'Volver a verificar'; ?>"/>
						</div>

					<?php elseif($step === 2): ?>

						<legend>Base de datos</legend>
						<p class="lead">Ingresa tus datos de conexi&oacute;n a la base de datos.</p>

						<?php if(!empty($message)): ?>
							<div class="error"><?= $message ?></div>
						<?php endif; ?>

						<dl>
							<dt>
								<label for="hostname">Servidor:</label>
								<small>Donde est&aacute; la base de datos, ej: <strong>localhost</strong></small>
							</dt>
							<dd><input type="text" autocomplete="off" id="hostname" name="dbhost" placeholder="localhost" value="<?= $db['hostname']; ?>" required/></span></dd>
						</dl>
						<dl>
							<dt>
								<label for="username">Usuario:</label>
								<small>El usuario de tu base de datos.</small>
							</dt>
							<dd><input type="text" autocomplete="off" id="username" name="dbuser" placeholder="root" value="<?= $db['username']; ?>" required/></span></dd>
						</dl>
						<dl>
							<dt>
								<label for="password">Contrase&ntilde;a:</label>
								<small>Para acceder a la base de datos.</small>
							</dt>
							<dd><input type="password" autocomplete="off" id="password" name="dbpass" placeholder="******" value="<?= $db['password']; ?>" /></span></dd>
						</dl>
						<dl>
							<dt>
								<label for="database">Base de datos</label>
								<small>Nombre de la base de datos para tu web.</small>
							</dt>
							<dd><input type="text" autocomplete="off" id="database" name="dbname" placeholder="mydblocalhost" value="<?= $db['database']; ?>" required/></span></dd>
						</dl>
						<div class="buttons">
							<input type="submit" value="Continuar &raquo;"/>
						</div>
					
					<?php elseif($step === 3): ?>

						<legend>Datos del sitio</legend>
						
						<?php if(!empty($message)): ?>
							<div class="error"><?= $message ?></div>
						<?php endif; ?>

						<dl>
							<dt>
								<label for="titulo">Nombre:</label>
								<small>El t&iacute;tulo de tu web.</small>
							</dt>
							<dd><input type="text" id="titulo" name="titulo" placeholder="<?= $config['script']['name'] ?>" value="<?= $site['titulo'] ?>" required/></dd>
						</dl>
						<dl>
							<dt>
								<label for="slogan">Lema:</label>
								<small>Ej: Inteligencia recargada.</small>
							</dt>
							<dd><input type="text" id="slogan" name="slogan" placeholder="<?= $config['script']['slogan'] ?>" value="<?= $site['slogan'] ?>" required/></small></dd>
						</dl>
						<dl>
							<dt>
								<label for="url">Direcci&oacute;n:</label>
								<small>Ingresa la url donde  est&aacute; alojada tu web, sin la &uacute;ltima diagonal <strong>/</strong> </small></dt>
							<dd><input type="text" id="url" name="url" value="<?= $site['url'] ?>" required/></dd>
						</dl>
						<dl>
							<dt>
								<label for="email">Email:</label>
								<small>Email de la web o del administrador.</small>
							</dt>
							<dd><input type="text" id="email" name="email" placeholder="noreply@example.com" value="<?= $site['email'] ?>" required/></dd>
						</dl>
						<legend>Datos de reCAPTCHA</legend>
						<p class="lead">Obtén tu clave desde <a href="https://www.google.com/recaptcha/admin" target="_blank"><strong>www.google.com/recaptcha/admin</strong></a></p>
						<dl>
							<dt>
								<label for="pkey">Clave pública del sitio:</label>
							</dt>
							<dd><input type="text" id="pkey" name="pkey" placeholder="<?= $config['pkey'] ?>" value="<?= $site['pkey'] ?>" required /></dd>
						</dl>
						<dl>
							<dt>
								<label for="skey">Clave secreta:</label>
							</dt>
							<dd><input type="text" id="skey" name="skey" placeholder="<?= $config['skey'] ?>" value="<?= $site['skey'] ?>" required/></dd>
						</dl>
						<div class="buttons">
							<input type="submit" value="Continuar &raquo;"/>
						</div>

					<?php elseif($step === 4): ?>

						<legend>Datos del administrador</legend>
						<p class="lead">Ingresa tus datos de usuario, m&aacute;s adelante debes editar tu cuenta para ingresar datos como, fecha de nacimiento, lugar de residencia, etc.</p>

						<?php if(!empty($message)): ?>
							<div class="error"><?= $message ?></div>
						<?php endif; ?>

						<dl>
							<dt><label for="username">Nombre de usuario:</label></dt>
							<dd><input type="text" id="username" name="username" autocomplete="off" placeholder="JohnDoe" value="<?= $user['username']; ?>" required/></dd>
						</dl>
						<dl>
							<dt>
								<label for="email">Email:</label>
								<small>Ingresa tu direcci&oacute;n de email.</small>
							</dt>
							<dd><input type="email" id="email" name="email" autocomplete="off" placeholder="jhondoe@some.com" value="<?= $user['email']; ?>" required/></dd>
						</dl>
						<dl>
							<dt><label for="password">Contrase&ntilde;a:</label></dt>
							<dd class="flex-col">
								<div id="strength" class="strength"></div>
								<input type="text" id="password" name="password" data-password="true" placeholder="#myPassLarge2025" autocomplete="off" value="<?= $user['password']; ?>" required/>
								<ul id="rules">
									<li id="symbol" class="invalid">🔒 Contiene al menos un símbolo</li>
									<li id="uppercase" class="invalid">🔡 Contiene al menos una mayúscula</li>
									<li id="number" class="invalid">🔢 Contiene al menos un número</li>
									<li id="length" class="invalid">📏 Mínimo 8 caracteres</li>
							  </ul>
							</dd>
						</dl>

						<div class="buttons">
							<input type="submit" value="Continuar &raquo;"/>
						</div>
						<script>
							const password = document.getElementById('password');
							const rules = {
								symbol: /[^A-Za-z0-9]/,
								uppercase: /[A-Z]/,
								number: /\d/,
								length: /.{8,}/
							};

							const statusElements = {
								symbol: document.getElementById('symbol'),
								uppercase: document.getElementById('uppercase'),
								number: document.getElementById('number'),
								length: document.getElementById('length'),
							};

							function verificarPassword() {
								let pass = password.value;
								let score = 0;

								for (let rule in rules) {
									if (rules[rule].test(pass)) {
										statusElements[rule].classList.remove('invalid');
										statusElements[rule].classList.add('valid');
										score++;
									} else {
										statusElements[rule].classList.remove('valid');
										statusElements[rule].classList.add('invalid');
									}
								}

								// Visualizar la fuerza de la contraseña
								const strength = document.getElementById('strength');
								if (score === 4) {
									strength.textContent = "✔ Contraseña fuerte";
									strength.style.color = 'green';
									password.classList.add('ok');
									password.classList.remove('fail');
								} else if (score >= 2) {
									strength.textContent = "⚠ Contraseña media";
									strength.style.color = 'orange';
									password.classList.add('fail');
									password.classList.remove('ok');
								} else {
									strength.textContent = "❌ Contraseña débil";
									strength.style.color = 'red';
									password.classList.add('fail');
									password.classList.remove('ok');
								}
							}

							password.addEventListener('input', () => verificarPassword());
							window.addEventListener('DOMContentLoaded', () => {
							if (password.value.trim() !== '') {
								password.dispatchEvent(new Event('input'));
							}
						});
						</script>
					
					<?php elseif($step === 5): ?>


						<h2>💚 ¡Gracias por instalar <?= $config['script']['name'] ?>!</h2>

						<p class="lead">Tu nueva comunidad <strong>Link Sharing System</strong> ya está lista para comenzar a compartir.</p>
						<p class="lead">Iniciá sesión con tus datos y explorá este espacio que sigue vivo gracias al esfuerzo de quienes creemos que aún vale la pena.<br>Esta actualización fue creada con dedicación, cariño y la firme decisión de no dejar morir algo que aún tiene mucho por dar.<br>No te olvides de <a href="http://github.com/isidromlc/PHPost" target="_blank" style="font-weight: 600;">visitarnos</a> para estar al tanto de futuras mejoras, y si encontrás algún error, ¡avisanos! Así seguimos creciendo, entre todos.</p>
						<p class="lead" style="text-align: center;">¡Bienvenido de nuevo a la comunidad que nunca se rinde! ✊</p>
					
						<div class="buttons">
							<input type="submit" value="Finalizar" />
						</div>
						<!-- ESTADISTICAS -->
						<div class="error">Ingresa a tu FTP y borra la carpeta <strong><?php echo basename(getcwd()); ?></strong> antes de usar el script.</div>

					<?php endif; ?>
					<!-- Fin -->
				</fieldset>
			</form>
		</section>
		<footer>
			<p>Powered by <a href="https://github.com/joelmiguelvalente" target="_blank">Miguel92</a></p>
			<small>Versión actual: v<strong><?= $Globals->getVersion('version') ?></strong></small>
		</footer>
	</main>
	
</body>
</html>