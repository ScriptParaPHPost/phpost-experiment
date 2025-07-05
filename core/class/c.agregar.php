<?php if ( ! defined('PHPOST_CORE_LOADED')) exit('No se permite el acceso directo al script');
/**
 * Clase para el manejo de los posts
 *
 * @name    c.posts.php
 * @author  PHPost Team
 */

class tsAgregar {
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*\
								PUBLICAR POSTS
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	/** simiPosts($q)
	 * @access public
	 * @param string
	 * @return array
	 */
	public function simiPosts($q)
	{
		global $tsUser, $tsCore;
		$query = db_exec([__FILE__, __LINE__], 'query', 'SELECT p.post_id, p.post_title, c.c_seo FROM p_posts AS p LEFT JOIN u_miembros AS u ON u.user_id = p.post_user LEFT JOIN p_categorias AS c ON c.cid = p.post_category WHERE p.post_status = \'0\' '.($tsUser->is_admod && $tsCore->settings['c_see_mod'] == 1 ? '' : '&& u.user_activo = \'1\' && u.user_baneado = \'0\'').' && MATCH(p.post_title) AGAINST(\''.$q.'\' IN BOOLEAN MODE) ORDER BY RAND() DESC LIMIT 5');
		$data = result_array($query);
		
		//
		return $data;
	}
	/** genTags($q)
	* @access public
	* @param string
	* @return string
   */
   public function genTags(string $q = '') {
	  $texto = preg_replace('/ {2,}/si', " ", trim(preg_replace("/[^ A-Za-z0-9]/", "", $q)));
	  $array = [];
	  # Solo agregamos de más de 4 y menos de 8 letras
	  foreach (explode(' ', $texto) as $tag):
		 if(strlen($tag) >= 4 AND strlen($tag) <= 12) array_push($array, strtolower($tag));
	  endforeach;
	  return join(', ', $array);
   }
	/*
		getPreview()
	*/
	function getPreview(){
		global $tsCore;
		//
		$titulo = $tsCore->setSecure($_POST['titulo'], true);
		$cuerpo = $tsCore->setSecure($_POST['cuerpo'], true);
		//
		return array('titulo' => $titulo, 'cuerpo' => $tsCore->parseBadWords($tsCore->parseBBCode($cuerpo), true));
	}
	/*
		validTags($tags)
	*/
	function validTags($tags){
		$tags = trim(preg_replace('/[^ A-Za-z0-9,]/', '', $tags));
		$tags = str_replace(' ','',$tags);
		if(empty($tags)) return false;
		else {
			$tags = explode(',',$tags);
			if(count($tags) < 4) return false;
			foreach($tags as $val){
				if(empty($val)) return false;
			}   
		}
		//
		return true;
	}
	/*
		newPost()
	*/
	function newPost(){
		global $tsCore, $tsUser, $tsNotificaciones, $tsActividad;
		//
		if($tsUser->is_admod || $tsUser->permisos['gopp']){
		//
		$postData = array(
			'date' => time(),
			'title' => $tsCore->parseBadWords($tsCore->setSecure($_POST['titulo'], true)),2,
			'body' => $tsCore->setSecure($_POST['cuerpo']),
			'tags' => $tsCore->parseBadWords($tsCore->setSecure($_POST['tags'], true)),true,1,
			'category' => intval($_POST['categoria']),
		);
		//ANTIFLOOD
		$antiflood = 2;
		$d = db_exec('fetch_row', db_exec([__FILE__, __LINE__], 'query', 'SELECT COUNT(post_id) AS few FROM `p_posts` WHERE post_body = \''.$postData['body'].'\' LIMIT 1'));
		if($d[0]) die('No se puede agregar el post. C&oacute;digo de error [#0aP]');
		// VACIOS
		foreach($postData as $key => $val){
			$val = trim(preg_replace('/[^ A-Za-z0-9]/', '', $val));
			$val = str_replace(' ', '', $val);
			if(empty($val)) return 0;
		}
		// TAGS
		$tags = $this->validTags($postData['tags']);
		if(empty($tags)) return 'Tienes que ingresar por lo menos <b>4</b> tags.';
		// ESTOS PUEDEN IR VACIOS
		$postData['visitantes'] = empty($_POST['visitantes']) ? 0 : 1;
		$postData['smileys'] = empty($_POST['smileys']) ? 0 : 1;
		$postData['private'] = empty($_POST['privado']) ? 0 : 1;
		$postData['block_comments'] = empty($_POST['sin_comentarios']) ? 0 : 1;
		// SOLO MODERADORES Y ADMINISTRADORES
		if(empty($tsUser->is_admod)  && $tsUser->permisos['most'] == false) {
			$postData['sponsored'] = 0;
			$postData['sticky'] = 0;   
		} else {
			$postData['sponsored'] = empty($_POST['patrocinado']) ? 0 : 1;
			$postData['sticky'] = empty($_POST['sticky']) ? 0 : 1;
		}
		// ANTI FLOOD
		if($tsUser->info['user_lastpost'] < (time() - $antiflood)) {
			// EXISTE LA CATEGORIA?
			$query = db_exec([__FILE__, __LINE__], 'query', 'SELECT cid FROM p_categorias WHERE cid = \''.(int)$postData['category'].'\' LIMIT 1');
			if(db_exec('num_rows', $query) == 0) return 'La categor&iacute;a especificada no existe.';
			// INSERTAMOS
			$_SERVER['REMOTE_ADDR'] = $_SERVER['X_FORWARDED_FOR'] ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
			if(!filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) { die('0: Su ip no se pudo validar.'); }
			if(db_exec([__FILE__, __LINE__], 'query', 'INSERT INTO `p_posts` (post_user, post_category, post_title, post_body, post_date, post_tags, post_ip, post_private, post_block_comments, post_sponsored, post_sticky, post_smileys, post_visitantes, post_status) VALUES (\''.$tsUser->uid.'\', \''.(int)$postData['category'].'\', \''.$postData['title'].'\',  \''.$postData['body'].'\', \''.$postData['date'].'\', \''.$postData['tags'].'\', \''.$_SERVER['REMOTE_ADDR'].'\', \''.(int)$postData['private'].'\', \''.(int)$postData['block_comments'].'\', \''.(int)$postData['sponsored'].'\', \''.(int)$postData['sticky'].'\', \''.(int)$postData['smileys'].'\', \''.(int)$postData['visitantes'].'\', '.(!$tsUser->is_admod && ($tsCore->settings['c_desapprove_post'] == 1 || $tsUser->permisos['gorpap'] == true) ? '\'3\'' : '\'0\'').')')) {
				$postID = db_exec('insert_id');
				// Si está oculto, lo creamos en el historial e.e
				if(!$tsUser->is_admod && ($tsCore->settings['c_desapprove_post'] == 1 || $tsUser->permisos['gorpap'] == true)) db_exec([__FILE__, __LINE__], 'query', 'INSERT INTO `w_historial` (`pofid`, `action`, `type`, `mod`, `reason`, `date`, `mod_ip`) VALUES (\''.(int)$postID.'\', \'3\', \'1\', \''.$tsUser->uid.'\', \'Revisi&oacute;n al publicar\', \''.time().'\', \''.$_SERVER['REMOTE_ADDR'].'\')');
				$time = time();
				// ESTADÍSTICAS
				db_exec([__FILE__, __LINE__], 'query', 'UPDATE `w_stats` SET `stats_posts` = stats_posts + \'1\' WHERE `stats_no` = \'1\'');
				// ULTIMO POST
				db_exec([__FILE__, __LINE__], 'query', 'UPDATE u_miembros SET user_lastpost = \''.$time.'\' WHERE user_id = \''.$tsUser->uid.'\'');
				// AGREGAR AL MONITOR DE LOS USUARIOS QUE ME SIGUEN
				$tsNotificaciones->setFollowNotificacion(5, 1, $tsUser->uid, $postID);
				// REGISTRAR MI ACTIVIDAD
				$tsActividad->setActividad(1, $postID);
				// SUBIR DE RANGO?
				$this->subirRango($tsUser->uid);
				//
				return $postID;
			} else return show_error('Error al ejecutar la consulta de la l&iacute;nea '.__LINE__.' de '.__FILE__.'.', 'db');
		} else return -1;
	} else return 'No tienes permiso para crear posts.';
  }
	/*
		savePost()
	*/
	function savePost(){
		global $tsCore, $tsUser;
		//
		$post_id = (int)$_GET['pid'];
		$query = db_exec([__FILE__, __LINE__], 'query', 'SELECT post_user, post_sponsored, post_sticky, post_status FROM p_posts WHERE post_id = \''.(int)$post_id.'\' LIMIT 1');
		$data = db_exec('fetch_assoc', $query);
		//
		if($data['post_status'] != '0' && !$tsUser->is_admod && !$tsUser->permisos['moedpo']) {
			return 'El post no puede ser editado.';
		}
		//
		$postData = array(
			'title' => $tsCore->parseBadWords($_POST['titulo'], true),
			'body' => $tsCore->setSecure($_POST['cuerpo'], true),
			'tags' => $tsCore->parseBadWords($tsCore->setSecure($_POST['tags'], true)),
			'category' => $_POST['categoria'],
		);
		// VACIOS
		foreach($postData as $key => $val){
			$val = trim(preg_replace('/[^ A-Za-z0-9]/', '', $val));
			$val = str_replace(' ', '', $val);
			if(empty($val)) return 0;
		}
		// TAGS
		$tags = $this->validTags($postData['tags']);
		if(empty($tags)) return 'Tienes que ingresar por lo menos <b>4</b> tags.';
		//
		$postData['visitantes'] = empty($_POST['visitantes']) ? 0 : 1;
		$postData['smileys'] = empty($_POST['smileys']) ? 0 : 1;			
		$postData['private'] = empty($_POST['privado']) ? 0 : 1;
		$postData['block_comments'] = empty($_POST['sin_comentarios']) ? 0 : 1;
		// SOLO MODERADORES Y ADMINISTRADORES
		if(empty($tsUser->is_admod)  && $tsUser->permisos['most'] == false) {
			$postData['sponsored'] = $data['post_sponsored'];
			$postData['sticky'] = $data['post_sticky'];   
		} else {
			$postData['sponsored'] = empty($_POST['patrocinado']) ? 0 : 1;
			$postData['sticky'] = empty($_POST['sticky']) ? 0 : 1;
		}
		// ACTUALIZAMOS
		if($tsUser->uid == $data['post_user'] || !empty($tsUser->is_admod) || !empty($tsUser->permisos['moedpo'])){
			if(db_exec([__FILE__, __LINE__], 'query', 'UPDATE p_posts SET post_title = \''.$postData['title'].'\', post_body = \''.$postData['body'].'\', post_tags = \''.$tsCore->setSecure($postData['tags']).'\', post_category = \''.(int)$postData['category'].'\', post_private = \''.$postData['private'].'\', post_block_comments = \''.$postData['block_comments'].'\', post_sponsored = \''.$postData['sponsored'].'\', post_smileys = \''.$postData['smileys'].'\', post_visitantes = \''.$postData['visitantes'].'\', post_sticky = \''.$postData['sticky'].'\' WHERE post_id = \''.(int)$post_id.'\'') or exit( show_error('Error al ejecutar la consulta de la l&iacute;nea '.__LINE__.' de '.__FILE__.'.', 'db') )) {
				 // GUARDAR EN EL HISTORIAL	DE MODERACION		 
				 if(($tsUser->is_admod || $tsUser->permisos['moedpo']) && $tsUser->uid != $data['post_user'] && $_POST['razon']){
					 include("c.moderacion.php");
					 $tsMod = new tsMod();
					 return $tsMod->setHistory('editar', 'post', array('post_id' => $post_id, 'title' => $postData['title'], 'autor' => $data['post_user'], 'razon' => $_POST['razon']));
				 } else return 1;
			}
		}
	}
	/*
		getEditPost()
	*/
	function getEditPost(){
		global $tsCore, $tsUser;
		//
		$pid = intval($_GET['pid']);
		//
		$query = db_exec([__FILE__, __LINE__], 'query', 'SELECT * FROM p_posts WHERE post_id = \''.(int)$pid.'\' LIMIT 1');
		$ford = db_exec('fetch_assoc', $query);
		
		//
		if(empty($ford['post_id'])){
			return 'El post elegido no existe.';
		}elseif($ford['post_status'] != '0' && $tsUser->is_admod == 0 && $tsUser->permisos['moedpo'] == false){
			return 'El post no puede ser editado.';
		}elseif(($tsUser->uid != $ford['post_user']) && $tsUser->is_admod == 0 && $tsUser->permisos['moedpo'] == false){
			return 'No puedes editar un post que no es tuyo.';
		}
		// PEQUEÑO HACK
		foreach($ford as $key => $val){
			$iden = str_replace('post_','b_',$key);
			$data[$iden] = $val;
		}
		//
		return $data;
	}

}