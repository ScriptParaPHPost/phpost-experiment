<?php

/**
 * PHPost 2025
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

if (!defined('PHPOST_CORE_LOADED')) 
   exit('Acceso denegado: ¡No puedes acceder este script directamente!');

$Config = $container->loader(
   'utils/Config.php', 'Config', 
   fn() => new Config(__DIR__ . '/../../storage/config.inc.php')
);

$dev = $Config->get('dev');

if (!isset($tsUser) || !is_object($tsUser)) {
   $tsUser = new stdClass();
   $tsUser->is_admod = false;
}

/**
 * Nueva forma de conectar a la base de datos
 */
try {
   /**
    * Nueva forma de conectar a la base de datos
    * Realizamos la conexión con MySQLi
    * @link https://www.php.net/manual/es/mysqli.construct.php
   */
   $mysqli = new mysqli(
      $Config->get('db.hostname'), 
      $Config->get('db.username'), 
      $Config->get('db.password'), 
      $Config->get('db.database')
   );

   // Comprobar el estado de la conexión
   if ($mysqli->connect_errno) {
      throw new Exception("Falló la conexión con MySQL: ({$mysqli->connect_errno}) {$mysqli->connect_error}");
   }
   // Establecer el juego de caracteres utf8
   if (!$mysqli->set_charset( $Config->get('db.charset') )) {
      throw new Exception('No se pudo establecer la codificación de caracteres.');
   }
} catch (Exception $e) {
   show_error($e->getMessage(), 'db');
}

/**
 * Ejecuta operaciones relacionadas con MySQLi de forma centralizada.
 *
 * Esta función sirve como una especie de "puerta de enlace" para distintos tipos de acciones
 * sobre la base de datos (consultas, escape de cadenas, obtener resultados, etc.).
 * Además, maneja errores de forma segura y opcionalmente muestra información detallada si está habilitado el modo desarrollador.
 *
 * @global mysqli    $mysqli  Conexión activa a MySQLi.
 * @global object    $tsUser  Objeto del usuario actual, usado para verificar permisos.
 * @global bool      $tsAjax  Indica si la petición es AJAX.
 * @global array     $req     Array global con información de configuración o entorno.
 *
 * @param mixed      $info    Información adicional como archivo/linea (para debug) o tipo de acción.
 * @param string     $type    Tipo de operación a realizar: 'query', 'real_escape_string', 'num_rows', etc.
 * @param mixed      $data    Datos requeridos para la operación (consulta SQL, objeto de resultado, etc.).
 *
 * @return mixed     Resultado de la operación según el tipo. Puede ser un booleano, array, objeto o null si falla.
 */
function db_exec() {
   global $mysqli, $tsUser, $tsAjax, $dev;

   $args = func_get_args();
   $info = $args[0] ?? null;
   $type = $args[1] ?? null;
   $data = $args[2] ?? null;

   // Si el primer parámetro es un array, contiene información adicional para debugging
   if (is_array($info)) {
      if (!$tsUser->is_admod && !$dev) {
         $info[0] = explode('\\', $info[0]);
      }
      $info['file']  = ($tsUser->is_admod || $dev) ? $info[0] : end($info[0]);
      $info['line']  = $info[1];
      $info['query'] = $data;
   } else {
      // Modo simplificado
      $data = $type;
      $type = $info;
      if ($type === 'query') {
         $info = [];
         $info['query'] = $data;
      }
   }

   // Ejecuta el tipo de acción solicitada
   return match ($type) {
      'query' => !empty($data) ? (function() use ($mysqli, $data, $info, $tsAjax, $dev) {
         try {
            $query = $mysqli->query($data);
            if (!$query) {
               show_error('No se pudo ejecutar una consulta en la base de datos. ' . $mysqli->error, 'Conectar', $info);
            }
            return $query;
         } catch (Exception $e) {
            if (!$tsAjax && $dev && ($info['file'] || $info['line'] || ($info['query'] && $tsUser->is_admod))) {
               show_error('No se pudo ejecutar una consulta en la base de datos.', 'db', $info);
            }
         }
      })() : null,

      'real_escape_string' => $mysqli->real_escape_string($data),
      'num_rows'           => $data->num_rows,
      'fetch_assoc'        => $data->fetch_assoc(),
      'fetch_array'        => $data->fetch_array(MYSQLI_ASSOC),
      'fetch_row'          => $data->fetch_row(),
      'free_result'        => $data->free(),
      'insert_id'          => $mysqli->insert_id,
      'error'              => $mysqli->error,

      default              => null,
   };
}

/**
 * Carga todos los resultados de un objeto mysqli_result en un array asociativo.
 *
 * Esta función recorre todo el resultado de una consulta y devuelve los datos
 * en un array de arrays, donde cada subarray representa una fila.
 *
 * @param mysqli_result $result  Objeto de resultado de una consulta MySQLi.
 *
 * @return array  Lista de filas como arrays asociativos. Retorna un array vacío si el parámetro no es válido.
 */
function result_array($result): array {
   if (!($result instanceof mysqli_result)) {
      return [];
   }
   $array = [];
   while ($row = db_exec('fetch_assoc', $result)) {
      $array[] = $row;
   }
   return $array;
}

/**
 * Muestra un error en pantalla utilizando una plantilla HTML amigable.
 *
 * Esta función se encarga de presentar errores, especialmente de base de datos, 
 * con un diseño limpio. Muestra información extra solo si el usuario es administrador 
 * o está en modo desarrollador.
 *
 * @param string $error  Mensaje de error a mostrar. Por defecto: 'Indefinido'.
 * @param string $type   Tipo de error: puede ser 'db' para errores de base de datos u otro identificador.
 * @param array $info    Información adicional sobre el error (archivo, línea, consulta).
 *
 * @return void
 */
function show_error(string $error = 'Indefinido', string $type = 'db', array $info = []): void {
   global $mysqli, $tsUser, $dev;

   $table = '';

   if ($type === 'db') {
      $extra = [];

      if ($tsUser->is_admod || $dev) {
         $extra[] = '<tr><td colspan="2"><p class="warning">' . htmlspecialchars($mysqli->error, ENT_QUOTES, 'UTF-8') . '</p></td></tr>';
      }

      if (isset($info['file'])) {
         $path = pathinfo($info['file']);
         $extra[] = '<tr><td>Ruta</td><td>' . htmlspecialchars($path['dirname'], ENT_QUOTES, 'UTF-8') . '</td></tr>';
         $extra[] = '<tr><td>Archivo</td><td>' . htmlspecialchars($path['basename'], ENT_QUOTES, 'UTF-8') . '</td></tr>';
      }

      if (isset($info['line'])) {
         $extra[] = '<tr class="alt"><td>Línea</td><td>' . (int) $info['line'] . '</td></tr>';
      }

      if (isset($info['query']) && ($tsUser->is_admod || $dev)) {
         $code = htmlspecialchars($info['query'], ENT_QUOTES, 'UTF-8');
         $extra[] = "<tr><td colspan=\"2\"><pre><code class=\"language-php\">$code</code></pre></td></tr>";
      }

      $table = '<table border="0"><tbody>' . implode('', $extra) . '</tbody></table>';
   }

   $title = $type === 'db' ? 'Base de datos' : ucfirst($type);

   $plantilla = file_get_contents(__DIR__ . '/../templates/errordb.html');

   $contenido = str_replace(
      ['{{title}}', '{{error}}', '{{table}}'],
      [$title, $error, $table],
      $plantilla
   );

   exit($contenido);
}

// Borramos la variable por seguridad
unset($db);

function ip_banned() {
   $IPBAN = (isset($_SERVER["X_FORWARDED_FOR"])) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
   if(!filter_var($IPBAN, FILTER_VALIDATE_IP)) exit('Su ip no se pudo validar.');
   if(db_exec( 'num_rows', db_exec([__FILE__, __LINE__], 'query', 
         "SELECT id FROM w_blacklist WHERE type = 1 && value = '{$IPBAN}' LIMIT 1"
   ))) die('Tu IP fue bloqueada por el administrador/moderador.');
}

function user_banned() {
   global $tsCore, $tsUser, $smarty;
   $banned_data = $tsUser->getUserBanned();

   if(!empty($banned_data)){
      if(empty($_GET['action'])){
         $smarty->assign([
            'tsTitle' => "Usuario baneado - {$tsCore->settings['titulo']}",
            'tsBanned' => $banned_data
         ]);
         $smarty->loadFilter('output', 'trimwhitespace');
         $smarty->display('suspension.tpl');

      } else die('<div class="emptyError">Usuario suspendido</div>');
      //
      exit;
   }

}

function site_in_maintenance() {
   global $tsCore, $tsUser, $smarty;
   if($tsCore->settings['offline'] == 1 && ($tsUser->is_admod != 1 && $tsUser->permisos['govwm'] == false) && $_GET['action'] != 'login-user'){
      $smarty->assign('tsTitle', "Sitio en mantenimiento - {$tsCore->settings['titulo']}");
      $smarty->assign('tsLogin', (isset($_GET["login"]) and $_GET["login"] == 'admin' ? true : false));

      if(empty($_GET["action"])) {
         $smarty->loadFilter('output', 'trimwhitespace');
         $smarty->display('mantenimiento.tpl');
      } else die('Espera un poco...');
      exit();
   }
}

function getSSL() {
   if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') $isSecure = false;
   elseif (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $isSecure = true;
   elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
      $isSecure = true;
   }
   $isSecure = ($isSecure == true) ? 'https://' : 'http://';
   return $isSecure;
}

/**
 * Función safe_count
 * @author Miguel92 
 * Actua igual que is_countable, excepto que este devuelve 
 * el valor y no un booleano
*/
if (!function_exists('safe_count')) {
   function safe_count($data, $mode = COUNT_NORMAL) {
      return (is_array($data) || $data instanceof Countable) ? count($data, $mode) : 0;
   }
}

if (!function_exists('safe_unserialize')) {
   function safe_unserialize($data) {
      return (!is_null($data) && ($data !== false || $data === 'b:0;')) ? unserialize($data) : [];
   }
}