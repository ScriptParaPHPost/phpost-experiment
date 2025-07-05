<?php

/**
 * database.php
 * @author      Miguel92
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

$phpost_mysqli['f_fotos'] = "CREATE TABLE IF NOT EXISTS `f_fotos` (
	`foto_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`f_title` varchar(40) NOT NULL DEFAULT '',
	`f_description` text NULL,
	`f_url` tinytext NULL,
	`f_user` int unsigned NOT NULL DEFAULT 0,
	`f_closed` tinyint(1) NOT NULL DEFAULT 0,
	`f_visitas` tinyint(1) NOT NULL DEFAULT 0,
	`f_status` tinyint(1) NOT NULL DEFAULT 0,
	`f_last` tinyint(1) NOT NULL DEFAULT 0,
	`f_hits` int unsigned NOT NULL DEFAULT 0,
	`f_ip` varchar(45) NOT NULL DEFAULT '',
	`f_date` int unsigned NOT NULL DEFAULT 0,
	INDEX `idx_user` (`f_user`),
	INDEX `idx_status` (`f_status`),
	INDEX `idx_user_status` (`f_user`, `f_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1 ;";

$phpost_mysqli['f_comentarios'] = "CREATE TABLE IF NOT EXISTS `f_comentarios` (
	`cid` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`c_foto_id` int unsigned NOT NULL DEFAULT 0,
	`c_user` int unsigned NOT NULL DEFAULT 0,
	`c_date` int unsigned NOT NULL DEFAULT 0,
	`c_body` text NULL,
	`c_ip` varchar(45) NOT NULL DEFAULT '',
	INDEX `idx_user` (`c_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1 ;";

$phpost_mysqli['f_votos'] = "CREATE TABLE IF NOT EXISTS `f_votos` (
	`vid` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`v_foto_id` int unsigned NOT NULL DEFAULT 0,
	`v_user` int unsigned NOT NULL DEFAULT 0,
	`v_type` tinyint(1) NOT NULL DEFAULT 0,
	`v_date` int unsigned NOT NULL DEFAULT 0,
	INDEX `idx_v_foto_id` (`v_foto_id`),
	INDEX `idx_v_user` (`v_user`),
	INDEX `idx_both` (`v_foto_id`, `v_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['p_borradores'] = "CREATE TABLE IF NOT EXISTS `p_borradores` (
	`bid` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`b_user` int unsigned NOT NULL DEFAULT 0,
	`b_date` int unsigned NOT NULL DEFAULT 0,
	`b_title` varchar(120) NOT NULL DEFAULT '',
	`b_body` text NULL,
	`b_portada` varchar(255) NOT NULL DEFAULT '',
	`b_tags` varchar(128) NOT NULL DEFAULT '',
	`b_category` int unsigned NOT NULL DEFAULT 0,
	`b_private` tinyint(1) NOT NULL DEFAULT 0,
	`b_block_comments` tinyint(1) NOT NULL DEFAULT 0,
	`b_sponsored` tinyint(1) NOT NULL DEFAULT 0,
	`b_sticky` tinyint(1) NOT NULL DEFAULT 0,
	`b_smileys` tinyint(1) NOT NULL DEFAULT 0,
	`b_visitantes` tinyint(1) NOT NULL DEFAULT 0,
	`b_post_id` int unsigned NOT NULL DEFAULT 0,
	`b_status` tinyint(1) NOT NULL DEFAULT 1,
	`b_causa` varchar(128) NOT NULL DEFAULT '',
	INDEX `idx_user` (`b_user`),
	INDEX `idx_status` (`b_status`),
	INDEX `idx_post_id` (`b_post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['p_categorias'] = "CREATE TABLE IF NOT EXISTS `p_categorias` (
	`cid` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`c_orden` int unsigned NOT NULL DEFAULT 0,
	`c_nombre` varchar(40) NOT NULL DEFAULT '',
	`c_seo` varchar(40) NOT NULL DEFAULT '',
	`c_img` varchar(40) NOT NULL DEFAULT 'comments.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['p_comentarios'] = "CREATE TABLE IF NOT EXISTS `p_comentarios` (
	`cid` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`c_post_id` int unsigned NOT NULL DEFAULT 0,
	`c_user` int unsigned NOT NULL DEFAULT 0,
	`c_date` int unsigned NOT NULL DEFAULT 0,
	`c_body` text NULL,
	`c_votos` smallint unsigned NOT NULL DEFAULT 0,
	`c_status` tinyint(1) NOT NULL DEFAULT	0,
	`c_ip` varchar(45) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['p_favoritos'] = "CREATE TABLE IF NOT EXISTS `p_favoritos` (
	`fav_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`fav_user` int unsigned NOT NULL DEFAULT 0,
	`fav_post_id` int unsigned NOT NULL DEFAULT 0,
	`fav_date` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['p_posts'] = "CREATE TABLE IF NOT EXISTS `p_posts` (
	`post_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`post_user` int unsigned NOT NULL DEFAULT 0,
	`post_category` int unsigned NOT NULL DEFAULT 0,
	`post_title` varchar(120) NOT NULL DEFAULT '',
	`post_body` text NULL,
	`post_portada` varchar(255) NOT NULL DEFAULT '',
	`post_date` int unsigned NOT NULL DEFAULT 0,
	`post_tags` varchar(128) NOT NULL DEFAULT '',
	`post_puntos` int unsigned NOT NULL DEFAULT 0,
	`post_comments` int unsigned NOT NULL DEFAULT 0,
	`post_seguidores` int unsigned NOT NULL DEFAULT 0,
	`post_shared` int unsigned NOT NULL DEFAULT 0,
	`post_favoritos` int unsigned NOT NULL DEFAULT 0,
	`post_cache` int unsigned NOT NULL DEFAULT 0,
	`post_hits` int unsigned NOT NULL DEFAULT 0,
	`post_ip` varchar(45) NOT NULL DEFAULT '',
	`post_private` tinyint(1) NOT NULL DEFAULT 0,
	`post_block_comments` tinyint(1) NOT NULL DEFAULT 0,
	`post_sponsored` tinyint(1) NOT NULL DEFAULT 0,
	`post_sticky` tinyint(1) NOT NULL DEFAULT 0,
	`post_smileys` tinyint(1) NOT NULL DEFAULT 0,
	`post_visitantes` tinyint(1) NOT NULL DEFAULT 0,
	`post_status` tinyint(1) NOT NULL DEFAULT 0,
	INDEX `idx_user` (`post_user`),
	INDEX `idx_status` (`post_status`),
	INDEX `idx_category` (`post_category`),
	INDEX `idx_date` (`post_date`),
	FULLTEXT `Search` (`post_title`, `post_body`, `post_tags`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['p_votos'] = "CREATE TABLE IF NOT EXISTS `p_votos` (
	`voto_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`tid` int unsigned NOT NULL DEFAULT 0,
	`tuser` int unsigned NOT NULL DEFAULT 0,
	`cant` int unsigned NOT NULL DEFAULT 0,
	`type` tinyint(1) NOT NULL DEFAULT 1,
	`date` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_actividad'] = "CREATE TABLE IF NOT EXISTS `u_actividad` (
	`ac_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_id` int unsigned NOT NULL DEFAULT 0,
	`obj_uno` int unsigned NOT NULL DEFAULT 0,
	`obj_dos` int unsigned NOT NULL DEFAULT 0,
	`ac_type` smallint NOT NULL DEFAULT 0,
	`ac_date` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_avisos'] = "CREATE TABLE IF NOT EXISTS `u_avisos` (
	`av_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_id` int unsigned NOT NULL DEFAULT 0,
	`av_subject` varchar(24) NOT NULL DEFAULT '',
	`av_body` text NULL,
	`av_date` int unsigned NOT NULL DEFAULT 0,
	`av_read` tinyint(1) NOT NULL DEFAULT 0,
	`av_type` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_bloqueos'] = "CREATE TABLE IF NOT EXISTS `u_bloqueos` (
	`bid` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`b_user` int unsigned NOT NULL DEFAULT 0,
	`b_auser` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_follows'] = "CREATE TABLE IF NOT EXISTS `u_follows` (
	`follow_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`f_user` int unsigned NOT NULL DEFAULT 0,
	`f_id` int unsigned NOT NULL DEFAULT 0,
	`f_type` tinyint(1) NOT NULL DEFAULT 0,
	`f_date` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_mensajes'] = "CREATE TABLE IF NOT EXISTS `u_mensajes` (
	`mp_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`mp_to` int unsigned NOT NULL DEFAULT 0,
	`mp_from` int unsigned NOT NULL DEFAULT 0,
	`mp_answer` tinyint(1) NOT NULL DEFAULT 0,
	`mp_read_to` tinyint(1) NOT NULL DEFAULT 0,
	`mp_read_from` tinyint(1) NOT NULL DEFAULT 1,
	`mp_read_mon_to` tinyint(1) NOT NULL DEFAULT 0,
	`mp_read_mon_from` tinyint(1) NOT NULL DEFAULT 1,
	`mp_del_to` tinyint(1) NOT NULL DEFAULT 0,
	`mp_del_from` tinyint(1) NOT NULL DEFAULT 0,
	`mp_subject` varchar(50) NOT NULL DEFAULT '',
	`mp_preview` varchar(75) NOT NULL DEFAULT '',
	`mp_date` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_miembros'] = "CREATE TABLE IF NOT EXISTS `u_miembros` (
	`user_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_name` varchar(20) NOT NULL DEFAULT '',
	`user_password` varchar(70) NOT NULL DEFAULT '',
	`user_email` varchar(60) NOT NULL DEFAULT '',
	`user_rango` int unsigned NOT NULL DEFAULT 3,
	`user_puntos` int unsigned NOT NULL DEFAULT 0,
	`user_posts` int unsigned NOT NULL DEFAULT 0,
	`user_comentarios` int unsigned NOT NULL DEFAULT 0,
	`user_seguidores` int unsigned NOT NULL DEFAULT 0,
	`user_cache` int unsigned NOT NULL DEFAULT 0,
	`user_puntosxdar` smallint unsigned NOT NULL DEFAULT 0,
	`user_bad_hits` smallint unsigned NOT NULL DEFAULT 0,
	`user_nextpuntos` int unsigned NOT NULL DEFAULT 0,
	`user_registro` int unsigned NOT NULL DEFAULT 0,
	`user_lastlogin` int unsigned NOT NULL DEFAULT 0,
	`user_lastactive` int unsigned NOT NULL DEFAULT 0,
	`user_lastpost` int unsigned NOT NULL DEFAULT 0,
	`user_last_ip` varchar(45) NOT NULL DEFAULT 0,
	`user_name_changes` int unsigned NOT NULL DEFAULT 3,
	`user_activo` tinyint(1) NOT NULL DEFAULT 0,
	`user_baneado` tinyint(1) NOT NULL DEFAULT 0,
	INDEX `idx_name` (`user_name`),
	INDEX `idx_email` (`user_email`),
	INDEX `idx_activo` (`user_activo`),
	INDEX `idx_baneado` (`user_baneado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_nicks'] = "CREATE TABLE IF NOT EXISTS `u_nicks` (
	`id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_id` int unsigned NOT NULL DEFAULT 0,
	`user_email` varchar(60) NOT NULL DEFAULT '',
	`name_1` varchar(20) NOT NULL DEFAULT '',
	`name_2` varchar(20) NOT NULL DEFAULT '',
	`hash` varchar(70) NOT NULL DEFAULT '',
	`time` int unsigned NOT NULL DEFAULT 0,
	`ip` varchar(45) NOT NULL DEFAULT '',
	`estado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_monitor'] = "CREATE TABLE IF NOT EXISTS `u_monitor` (
	`not_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_id` int unsigned NOT NULL DEFAULT 0,
	`obj_user` int unsigned NOT NULL DEFAULT 0,
	`obj_uno` int unsigned NOT NULL DEFAULT 0,
	`obj_dos` int unsigned NOT NULL DEFAULT 0,
	`obj_tres` int unsigned NOT NULL DEFAULT 0,
	`not_type` smallint NOT NULL DEFAULT 0,
	`not_date` int unsigned NOT NULL DEFAULT 0,
	`not_total` smallint NOT NULL DEFAULT 1,
	`not_menubar` tinyint(1) NOT NULL DEFAULT 2,
	`not_monitor` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_muro'] = "CREATE TABLE IF NOT EXISTS `u_muro` (
	`pub_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`p_user` int unsigned NOT NULL DEFAULT 0,
	`p_user_pub` int unsigned NOT NULL DEFAULT 0,
	`p_date` int unsigned NOT NULL DEFAULT 0,
	`p_body` text NULL,
	`p_likes` int unsigned NOT NULL DEFAULT 0,
	`p_favorites` int unsigned NOT NULL DEFAULT 0,
	`p_shared` int unsigned NOT NULL DEFAULT 0,
	`p_comments` int unsigned NOT NULL DEFAULT 0,
	`p_type` tinyint(1) NOT NULL DEFAULT 0,
	`p_privacity` tinyint(1) NOT NULL DEFAULT 0,
	`p_ip` varchar(45) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_muro_adjuntos'] = "CREATE TABLE IF NOT EXISTS `u_muro_adjuntos` (
	`adj_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`pub_id` int unsigned NOT NULL DEFAULT 0,
	`adj_title` varchar(100) NOT NULL DEFAULT '',
	`adj_url` varchar(255) NOT NULL DEFAULT '',
	`adj_image` varchar(255) NOT NULL DEFAULT '',
	`adj_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_muro_comentarios'] = "CREATE TABLE IF NOT EXISTS `u_muro_comentarios` (
	`cid` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`pub_id` int unsigned NOT NULL DEFAULT 0,
	`c_user` int unsigned NOT NULL DEFAULT 0,
	`c_date` int unsigned NOT NULL DEFAULT 0,
	`c_body` text NULL,
	`c_likes` int unsigned NOT NULL DEFAULT 0,
	`c_ip` varchar(45) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_muro_info'] = "CREATE TABLE IF NOT EXISTS `u_muro_info` (
	`iid` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`pub_id` int unsigned NOT NULL DEFAULT 0,
	`user_id` int unsigned NOT NULL DEFAULT 0,
	`obj_type` tinyint(1) NOT NULL DEFAULT 0,
	`tipo` enum('shared','favorites','likes') NOT NULL,
	`date` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_muro_tags'] = "CREATE TABLE IF NOT EXISTS `u_muro_tags` (
	`tag_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`obj_id` int unsigned NOT NULL DEFAULT 0,
	`tag_text` varchar(50) NOT NULL DEFAULT '',
	`date` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_pins'] = "CREATE TABLE IF NOT EXISTS `u_pins` (
	`p_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`p_user` int unsigned NOT NULL DEFAULT 0,
	`p_data` varchar(50) NOT NULL DEFAULT '',
	`date` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_perfil'] = "CREATE TABLE IF NOT EXISTS `u_perfil` (
	`user_id` int unsigned NOT NULL DEFAULT 0 PRIMARY KEY,
	`user_dia` tinyint(2) NOT NULL DEFAULT 0,
	`user_mes` tinyint(2) NOT NULL DEFAULT 0,
	`user_ano` smallint NOT NULL DEFAULT 0,
	`user_pais` varchar(2) NOT NULL DEFAULT 'XX',
	`user_estado` smallint NOT NULL DEFAULT 1,
	`user_sexo` tinyint(1) NOT NULL DEFAULT 1,
	`user_firma` text NULL,
	`p_nombre` varchar(32) NOT NULL DEFAULT '',
	`p_avatar` tinyint(1) NOT NULL DEFAULT 0,
	`p_mensaje` varchar(60) NOT NULL DEFAULT '',
	`p_sitio` varchar(255) NOT NULL DEFAULT '',
	`p_socials` text NULL,
	`p_configs` varchar(100) NOT NULL DEFAULT 'a:3:{s:1:\"m\";s:1:\"5\";s:2:\"mf\";i:5;s:3:\"rmp\";s:1:\"5\";}',
	`p_total` varchar(54) NOT NULL DEFAULT 'a:6:{i:0;i:5;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;}'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$phpost_mysqli['u_portal'] = "CREATE TABLE IF NOT EXISTS `u_portal` (
	`user_id` int unsigned NOT NULL DEFAULT 0 PRIMARY KEY,
	`last_posts_visited` text NULL,
	`last_posts_shared` text NULL,
	`last_posts_cats` text NULL,
	`c_monitor` text NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$phpost_mysqli['u_rangos'] = "CREATE TABLE IF NOT EXISTS `u_rangos` (
	`rango_id` int(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`r_name` varchar(32) NOT NULL DEFAULT '',
	`r_color` char(6) NOT NULL DEFAULT 171717,
	`r_image` varchar(32) NOT NULL DEFAULT 'new.png',
	`r_cant` smallint NOT NULL DEFAULT 0,
	`r_allows` text NULL,
	`r_type` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_respuestas'] = "CREATE TABLE IF NOT EXISTS `u_respuestas` (
	`mr_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`mp_id` int unsigned NOT NULL DEFAULT 0,
	`mr_from` int unsigned NOT NULL DEFAULT 0,
	`mr_body` text NULL,
	`mr_ip` varchar(45) NOT NULL DEFAULT '',
	`mr_date` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['u_sessions'] = "CREATE TABLE IF NOT EXISTS `u_sessions` (
	`session_id` varchar(32) NOT NULL DEFAULT '' PRIMARY KEY,
	`session_user_id` int unsigned unsigned NOT NULL DEFAULT 0,
	`session_ip` varchar(45) NOT NULL DEFAULT '',
	`session_time` int unsigned NOT NULL DEFAULT 0,
	`session_autologin` tinyint(1) NOT NULL DEFAULT 0,
	KEY `session_user_id` (`session_user_id`),
	KEY `session_time` (`session_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$phpost_mysqli['u_suspension'] = "CREATE TABLE IF NOT EXISTS `u_suspension` (
	`susp_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_id` int unsigned NOT NULL DEFAULT 0,
	`susp_causa` text NULL,
	`susp_date` int unsigned NOT NULL DEFAULT 0,
	`susp_termina` int unsigned NOT NULL DEFAULT 0,
	`susp_mod` int unsigned NOT NULL DEFAULT 0,
	`susp_ip` varchar(45) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['w_afiliados'] = "CREATE TABLE IF NOT EXISTS `w_afiliados` (
	`aid` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`a_titulo` varchar(35) NOT NULL DEFAULT '',
	`a_url` varchar(40) NOT NULL DEFAULT '',
	`a_banner` varchar(100) NOT NULL DEFAULT '',
	`a_descripcion` varchar(200) NOT NULL DEFAULT '',
	`a_sid` int unsigned NOT NULL DEFAULT 0,
	`a_hits_in` int unsigned NOT NULL DEFAULT 0,
	`a_hits_out` int unsigned NOT NULL DEFAULT 0,
	`a_date` int unsigned NOT NULL DEFAULT 0,
	`a_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['w_config_general'] = "CREATE TABLE IF NOT EXISTS `w_config_general` (
	`tscript_id` int unsigned NOT NULL DEFAULT 0 PRIMARY KEY,
	`titulo` varchar(24) NOT NULL DEFAULT '',
	`slogan` varchar(32) NOT NULL DEFAULT '',
	`url` varchar(255) NOT NULL DEFAULT '',
	`email` varchar(60) NOT NULL DEFAULT '',
	`banner` varchar(100) NOT NULL DEFAULT '',
	`tema_id` int unsigned NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$phpost_mysqli['w_config_ads'] = "CREATE TABLE IF NOT EXISTS `w_config_ads` (
	`tscript_id` int unsigned NOT NULL PRIMARY KEY,
	`ads_300` text NULL,
	`ads_468` text NULL,
	`ads_160` text NULL,
	`ads_728` text NULL,
	`ads_search` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$phpost_mysqli['w_config_users'] = "CREATE TABLE IF NOT EXISTS `w_config_users` (
	`tscript_id` int unsigned NOT NULL PRIMARY KEY,
	`c_last_active` smallint NOT NULL DEFAULT 0,
	`c_allow_sess_ip` tinyint(1) NOT NULL DEFAULT 1,
	`c_count_guests` tinyint(1) NOT NULL DEFAULT 0,
	`c_reg_active` tinyint(1) NOT NULL DEFAULT 1,
	`c_reg_activate` tinyint(1) NOT NULL DEFAULT 1,
	`c_reg_rango` int(5) NOT NULL DEFAULT 3,
	`c_met_welcome` tinyint(1) NOT NULL DEFAULT 0,
	`c_message_welcome` varchar(500) NOT NULL DEFAULT 'Hola {{usuario}}, {{bienvenida}} a [b]{{sitio}}[/b].',
	`c_fotos_private` int unsigned NOT NULL DEFAULT 0,
	`c_hits_guest` tinyint(1) NOT NULL DEFAULT 0,
	`c_keep_points` tinyint(1) NOT NULL DEFAULT 0,
	`c_allow_points` int unsigned NOT NULL DEFAULT 0,
	`c_allow_edad` int unsigned NOT NULL DEFAULT 16,
	`c_allow_sump` int unsigned NOT NULL DEFAULT 0,
	`c_allow_firma` tinyint(1) NOT NULL DEFAULT 1,
	`c_allow_upload` tinyint(1) NOT NULL DEFAULT 0,
	`c_allow_portal` tinyint(1) NOT NULL DEFAULT 1,
	`c_allow_live` tinyint(1) NOT NULL DEFAULT 1,
	`c_see_mod` tinyint(1) NOT NULL DEFAULT 0,
	`c_stats_cache` smallint unsigned NOT NULL DEFAULT 15,
	`c_desapprove_post` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$phpost_mysqli['w_config_limits'] = "CREATE TABLE IF NOT EXISTS `w_config_limits` (
	`tscript_id` int unsigned NOT NULL PRIMARY KEY,
	`c_max_posts` smallint NOT NULL DEFAULT 50,
	`c_max_com` smallint NOT NULL DEFAULT 50,
	`c_max_nots` smallint NOT NULL DEFAULT 99,
	`c_max_acts` smallint NOT NULL DEFAULT 99,
	`c_newr_type` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$phpost_mysqli['w_config_misc'] = "CREATE TABLE IF NOT EXISTS `w_config_misc` (
	`tscript_id` int unsigned NOT NULL PRIMARY KEY,
	`offline` tinyint(1) NOT NULL DEFAULT 0,
	`offline_message` varchar(255) NOT NULL DEFAULT 'Estamos en mantenimiento',
	`pkey` varchar(70) NOT NULL DEFAULT '',
	`skey` varchar(70) NOT NULL DEFAULT '',
	`version` varchar(26) NOT NULL DEFAULT '',
	`version_code` varchar(26) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$phpost_mysqli['w_denuncias'] = "CREATE TABLE IF NOT EXISTS `w_denuncias` (
	`did` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`obj_id` int unsigned NOT NULL DEFAULT 0,
	`d_user` int unsigned NOT NULL DEFAULT 0,
	`d_razon` smallint NOT NULL DEFAULT 0,
	`d_extra` text NULL,
	`d_total` tinyint(1) NOT NULL DEFAULT 1,
	`d_type` tinyint(1) NOT NULL DEFAULT 0,
	`d_date` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['w_contacts'] = "CREATE TABLE IF NOT EXISTS `w_contacts` (
	`id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_id` int unsigned NOT NULL DEFAULT 0,
	`user_email` varchar(100) NOT NULL DEFAULT '',
	`time` int(15) NOT NULL DEFAULT 0,
	`type` tinyint(1) NOT NULL DEFAULT 0,
	`hash` varchar(70) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['w_medallas'] = "CREATE TABLE IF NOT EXISTS `w_medallas` (
	`medal_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`m_autor` int unsigned NOT NULL DEFAULT 0,
	`m_title` varchar(35) NOT NULL DEFAULT '',
	`m_description` varchar(120) NOT NULL DEFAULT '',
	`m_image` varchar(120) NOT NULL DEFAULT '',
	`m_cant` int unsigned NOT NULL DEFAULT 0,
	`m_type` tinyint(1) NOT NULL DEFAULT 0,
	`m_cond_user` int unsigned NOT NULL DEFAULT 0,
	`m_cond_user_rango` int unsigned NOT NULL DEFAULT 0,
	`m_cond_post` int unsigned NOT NULL DEFAULT 0,
	`m_cond_foto` int unsigned NOT NULL DEFAULT 0,
	`m_date` int unsigned NOT NULL DEFAULT 0,
	`m_total` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['w_medallas_assign'] = "CREATE TABLE IF NOT EXISTS `w_medallas_assign` (
	`id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`medal_id` int unsigned NOT NULL DEFAULT 0,
	`medal_for` int unsigned NOT NULL DEFAULT 0,
	`medal_date` int unsigned NOT NULL DEFAULT 0,
	`medal_ip` varchar(45) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['w_historial'] = "CREATE TABLE IF NOT EXISTS `w_historial` (
	`id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`pofid` int unsigned NOT NULL DEFAULT 0,
	`type` tinyint(1) NOT NULL DEFAULT 0,
	`action` tinyint(1) NOT NULL DEFAULT 0,
	`mod` int unsigned NOT NULL DEFAULT 0,
	`reason` varchar(255) NOT NULL DEFAULT '',
	`date` int unsigned NOT NULL DEFAULT 0,
	`mod_ip` varchar(45) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['w_noticias'] = "CREATE TABLE IF NOT EXISTS `w_noticias` (
	`not_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`not_body` varchar(255) NOT NULL DEFAULT '',
	`not_autor` int unsigned NOT NULL DEFAULT 0,
	`not_date` int unsigned NOT NULL DEFAULT 0,
	`not_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['w_blacklist'] = "CREATE TABLE IF NOT EXISTS `w_blacklist` (
	`id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`type` tinyint(1) NOT NULL DEFAULT 0,
	`value` varchar(50) NOT NULL DEFAULT '',
	`reason` varchar(120) NOT NULL DEFAULT '',
	`author` int unsigned NOT NULL DEFAULT 0,
	`date` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;";

$phpost_mysqli['w_badwords'] = "CREATE TABLE IF NOT EXISTS `w_badwords` (
	`wid` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`word` varchar(255) NOT NULL DEFAULT '',
	`swop` varchar(255) NOT NULL DEFAULT '',
	`method` tinyint(1) NOT NULL DEFAULT 0,
	`type` tinyint(1) NOT NULL DEFAULT 0,
	`author` int unsigned NOT NULL DEFAULT 0,
	`reason` varchar(255) NOT NULL DEFAULT '',
	`date` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;";

$phpost_mysqli['w_stats'] = "CREATE TABLE IF NOT EXISTS `w_stats` (
	`stats_no` tinyint(1) NOT NULL DEFAULT 0 PRIMARY KEY,
	`stats_max_online` int unsigned NOT NULL DEFAULT 0,
	`stats_max_time` int unsigned NOT NULL DEFAULT 0,
	`stats_time` int unsigned NOT NULL DEFAULT 0,
	`stats_time_cache` int unsigned NOT NULL DEFAULT 0,
	`stats_time_foundation` int unsigned NOT NULL DEFAULT 0,
	`stats_time_upgrade` int unsigned NOT NULL DEFAULT 0,
	`stats_miembros` int unsigned NOT NULL DEFAULT 0,
	`stats_posts` int unsigned NOT NULL DEFAULT 0,
	`stats_fotos` int unsigned NOT NULL DEFAULT 0,
	`stats_comments` int unsigned NOT NULL DEFAULT 0,
	`stats_foto_comments` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$phpost_mysqli['w_temas'] = "CREATE TABLE IF NOT EXISTS `w_temas` (
	`tid` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`t_name` varchar(80) NOT NULL DEFAULT '',
	`t_url` varchar(255) NOT NULL DEFAULT '',
	`t_path` varchar(30) NOT NULL DEFAULT '',
	`t_copy` varchar(120) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;";

$phpost_mysqli['w_visitas'] = "CREATE TABLE IF NOT EXISTS `w_visitas` (
	`id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user` int unsigned NOT NULL DEFAULT 0,
	`for` int unsigned NOT NULL DEFAULT 0,
	`type` tinyint(1) NOT NULL DEFAULT 0,
	`date` int unsigned NOT NULL DEFAULT 0,
	`ip` varchar(45) NOT NULL DEFAULT '',
	INDEX `idx_for` (`for`),
	INDEX `idx_type` (`type`),
	INDEX `idx_user` (`user`),
	INDEX `idx_for_type_user` (`for`, `type`, `user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

$phpost_mysqli['ip_categorias'] = "INSERT INTO `p_categorias` (`cid`, `c_orden`, `c_nombre`, `c_seo`, `c_img`) VALUES
(1, 1, 'Animaciones', 'animaciones', 'flash.png'),
(2, 2, 'Apuntes y Monografías', 'apuntesymonografias', 'report.png'),
(3, 3, 'Arte', 'arte', 'palette.png'),
(4, 4, 'Autos y Motos', 'autosymotos', 'car.png'),
(5, 5, 'Celulares', 'celulares', 'phone.png'),
(6, 6, 'Ciencia y Educación', 'cienciayeducacion', 'lab.png'),
(7, 7, 'Comics', 'comics', 'comic.png'),
(8, 8, 'Deportes', 'deportes', 'sport.png'),
(9, 9, 'Downloads', 'downloads', 'disk.png'),
(10, 10, 'E-books y Tutoriales', 'ebooksytutoriales', 'ebook.png'),
(11, 11, 'Ecología', 'ecologia', 'nature.png'),
(12, 12, 'Economía y Negocios', 'economiaynegocios', 'economy.png'),
(13, 13, 'Femme', 'femme', 'female.png'),
(14, 14, 'Hazlo tu mismo', 'hazlotumismo', 'escuadra.png'),
(15, 15, 'Humor', 'humor', 'humor.png'),
(16, 16, 'Imágenes', 'imagenes', 'photo.png'),
(17, 17, 'Info', 'info', 'book.png'),
(18, 18, 'Juegos', 'juegos', 'controller.png'),
(19, 19, 'Links', 'links', 'link.png'),
(20, 20, 'Linux', 'linux', 'tux.png'),
(21, 21, 'Mac', 'mac', 'mac.png'),
(22, 22, 'Manga y Anime', 'mangayanime', 'manga.png'),
(23, 23, 'Mascotas', 'mascotas', 'pet.png'),
(24, 24, 'Música', 'musica', 'music.png'),
(25, 25, 'Noticias', 'noticias', 'newspaper.png'),
(26, 26, 'Off Topic', 'offtopic', 'comments.png'),
(27, 27, 'Recetas y Cocina', 'recetasycocina', 'cake.png'),
(28, 28, 'Salud y Bienestar', 'saludybienestar', 'heart.png'),
(29, 29, 'Solidaridad', 'solidaridad', 'salva.png'),
(30, 30, 'Taringa!', 'taringa', 'tscript.png'),
(31, 31, 'Turismo', 'turismo', 'brujula.png'),
(32, 32, 'TV, Peliculas y series', 'tvpeliculasyseries', 'tv.png'),
(33, 33, 'Videos On-line', 'videosonline', 'film.png');";

$phpost_mysqli['iu_rangos'] = "INSERT INTO `u_rangos` (`rango_id`, `r_name`, `r_color`, `r_image`, `r_cant`, `r_allows`, `r_type`) VALUES
(1, 'Administrador', 'D6030B', 'rosette.png', 0, 'a:4:{s:4:\"suad\";s:2:\"on\";s:4:\"goaf\";s:1:\"5\";s:5:\"gopfp\";s:2:\"20\";s:5:\"gopfd\";s:2:\"50\";}', 0),
(2, 'Moderador', 'ff9900', 'shield.png', 0, 'a:4:{s:4:\"sumo\";s:2:\"on\";s:4:\"goaf\";s:2:\"15\";s:5:\"gopfp\";s:2:\"18\";s:5:\"gopfd\";s:2:\"30\";}', 0),
(3, 'Novato', 171717, 'new.png', 0, 'a:12:{s:4:\"godp\";s:2:\"on\";s:4:\"gopp\";s:2:\"on\";s:5:\"gopcp\";s:2:\"on\";s:5:\"govpp\";s:2:\"on\";s:5:\"govpn\";s:2:\"on\";s:5:\"goepc\";s:2:\"on\";s:5:\"godpc\";s:2:\"on\";s:4:\"gopf\";s:2:\"on\";s:5:\"gopcf\";s:2:\"on\";s:4:\"goaf\";s:2:\"20\";s:5:\"gopfp\";s:1:\"5\";s:5:\"gopfd\";s:1:\"5\";}', 0),
(4, 'New Full User', '0198E7', 'star_bronze_3.png', 50, 'a:12:{s:4:\"godp\";s:2:\"on\";s:4:\"gopp\";s:2:\"on\";s:5:\"gopcp\";s:2:\"on\";s:5:\"govpp\";s:2:\"on\";s:5:\"govpn\";s:2:\"on\";s:5:\"goepc\";s:2:\"on\";s:5:\"godpc\";s:2:\"on\";s:4:\"gopf\";s:2:\"on\";s:5:\"gopcf\";s:2:\"on\";s:4:\"goaf\";s:2:\"20\";s:5:\"gopfp\";s:2:\"10\";s:5:\"gopfd\";s:2:\"10\";}', 1),
(5, 'Full User', '00ccff', 'star_silver_3.png', 70, 'a:12:{s:4:\"godp\";s:2:\"on\";s:4:\"gopp\";s:2:\"on\";s:5:\"gopcp\";s:2:\"on\";s:5:\"govpp\";s:2:\"on\";s:5:\"govpn\";s:2:\"on\";s:5:\"goepc\";s:2:\"on\";s:5:\"godpc\";s:2:\"on\";s:4:\"gopf\";s:2:\"on\";s:5:\"gopcf\";s:2:\"on\";s:4:\"goaf\";s:2:\"20\";s:5:\"gopfp\";s:2:\"12\";s:5:\"gopfd\";s:2:\"20\";}', 1),
(6, 'Great User', '01A021', 'star_gold_3.png', 0, 'a:12:{s:4:\"godp\";s:2:\"on\";s:4:\"gopp\";s:2:\"on\";s:5:\"gopcp\";s:2:\"on\";s:5:\"govpp\";s:2:\"on\";s:5:\"govpn\";s:2:\"on\";s:5:\"goepc\";s:2:\"on\";s:5:\"godpc\";s:2:\"on\";s:4:\"gopf\";s:2:\"on\";s:5:\"gopcf\";s:2:\"on\";s:4:\"goaf\";s:2:\"20\";s:5:\"gopfp\";s:2:\"11\";s:5:\"gopfd\";s:2:\"15\";}', 0),
(7, 'Gold User', 'cc6600', 'asterisk_yellow.png', 120, 'a:12:{s:4:\"godp\";s:2:\"on\";s:4:\"gopp\";s:2:\"on\";s:5:\"gopcp\";s:2:\"on\";s:5:\"govpp\";s:2:\"on\";s:5:\"govpn\";s:2:\"on\";s:5:\"goepc\";s:2:\"on\";s:5:\"godpc\";s:2:\"on\";s:4:\"gopf\";s:2:\"on\";s:5:\"gopcf\";s:2:\"on\";s:4:\"goaf\";s:2:\"20\";s:5:\"gopfp\";s:2:\"12\";s:5:\"gopfd\";s:2:\"25\";}', 1);";

$phpost_mysqli['iw_config_general'] = "INSERT INTO `w_config_general` (`tscript_id`) VALUES (1);";
$phpost_mysqli['iw_config_ads'] = "INSERT INTO `w_config_ads` (`tscript_id`) VALUES (1);";
$phpost_mysqli['iw_config_users'] = "INSERT INTO `w_config_users` (`tscript_id`) VALUES (1);";
$phpost_mysqli['iw_config_limits'] = "INSERT INTO `w_config_limits` (`tscript_id`) VALUES (1);";
$phpost_mysqli['iw_config_misc'] = "INSERT INTO `w_config_misc` (`tscript_id`) VALUES (1);";

$phpost_mysqli['iw_stats'] = "INSERT INTO `w_stats` (`stats_no`, `stats_max_online`) VALUES (1, 0);";