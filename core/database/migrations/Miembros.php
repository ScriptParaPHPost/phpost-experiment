<?php

return new class extends Migration {

   public function up(): void {

      Schema::create('u_actividad', function(Blueprint $table) {
         $table->id('ac_id');
         $table->integer('user_id')->unsigned()->default(0);
         $table->integer('obj_uno')->unsigned()->default(0);
         $table->integer('obj_dos')->unsigned()->default(0);
         $table->smallInteger('ac_type')->default(0);
         $table->integer('ac_date')->unsigned()->default(0);
      });

      Schema::create('u_avisos', function(Blueprint $table) {
         $table->id('av_id');
         $table->integer('user_id')->unsigned()->default(0);
         $table->string('av_subject', 24)->default('');
         $table->text('av_body')->nullable();
         $table->integer('av_date')->unsigned()->default(0);
         $table->tinyInteger('av_read')->default(0);
         $table->tinyInteger('av_type')->default(0);
      });

      Schema::create('u_bloqueos', function(Blueprint $table) {
         $table->id('bid');
         $table->integer('b_user')->unsigned()->default(0);
         $table->integer('b_auser')->unsigned()->default(0);
      });

      Schema::create('u_follows', function(Blueprint $table) {
         $table->id('follow_id');
         $table->integer('f_user')->unsigned()->default(0);
         $table->integer('f_id')->unsigned()->default(0);
         $table->tinyInteger('f_type')->default(0);
         $table->integer('f_date')->unsigned()->default(0);
      });

      Schema::create('u_miembros', function(Blueprint $table) {
         $table->id('user_id');
         $table->string('user_name', 20)->default('');
         $table->string('user_password', 70)->default('');
         $table->string('user_email', 60)->default('');
         $table->integer('user_rango')->unsigned()->default(3);
         $table->integer('user_puntos')->unsigned()->default(0);
         $table->integer('user_posts')->unsigned()->default(0);
         $table->integer('user_comentarios')->unsigned()->default(0);
         $table->integer('user_seguidores')->unsigned()->default(0);
         $table->integer('user_cache')->unsigned()->default(0);
         $table->smallInteger('user_puntosxdar')->unsigned()->default(0);
         $table->smallInteger('user_bad_hits')->unsigned()->default(0);
         $table->integer('user_nextpuntos')->unsigned()->default(0);
         $table->integer('user_registro')->unsigned()->default(0);
         $table->integer('user_lastlogin')->unsigned()->default(0);
         $table->integer('user_lastactive')->unsigned()->default(0);
         $table->integer('user_lastpost')->unsigned()->default(0);
         $table->string('user_last_ip', 45)->default('');
         $table->integer('user_name_changes')->unsigned()->default(3);
         $table->tinyInteger('user_activo')->default(0);
         $table->tinyInteger('user_baneado')->default(0);

         $table->addIndex('user_name');
         $table->addIndex('user_email');
         $table->addIndex('user_activo');
         $table->addIndex('user_baneado');
      });

      Schema::create('u_monitor', function(Blueprint $table) {
         $table->id('not_id');
         $table->integer('user_id')->unsigned()->default(0);
         $table->integer('obj_user')->unsigned()->default(0);
         $table->integer('obj_uno')->unsigned()->default(0);
         $table->integer('obj_dos')->unsigned()->default(0);
         $table->integer('obj_tres')->unsigned()->default(0);
         $table->smallInteger('not_type')->default(0);
         $table->integer('not_date')->unsigned()->default(0);
         $table->smallInteger('not_total')->default(1);
         $table->tinyInteger('not_menubar')->default(2);
         $table->tinyInteger('not_monitor')->default(1);
      });

      Schema::create('u_nicks', function(Blueprint $table) {
         $table->id('id');
         $table->integer('user_id')->unsigned()->default(0);
         $table->string('user_email', 60)->default('');
         $table->string('name_1', 20)->default('');
         $table->string('name_2', 20)->default('');
         $table->string('hash', 70)->default('');
         $table->integer('time')->unsigned()->default(0);
         $table->string('ip', 45)->default('');
         $table->tinyInteger('estado')->default(0);
      });

      Schema::create('u_perfil', function(Blueprint $table) {
         $table->integer('user_id')->unsigned()->default(0)->primary();
         $table->tinyInteger('user_dia')->default(0);
         $table->tinyInteger('user_mes')->default(0);
         $table->smallInteger('user_ano')->default(0);
         $table->string('user_pais', 2)->default('XX');
         $table->smallInteger('user_estado')->default(1);
         $table->tinyInteger('user_sexo')->default(1);
         $table->text('user_firma')->nullable();
         $table->string('p_nombre', 32)->default('');
         $table->tinyInteger('p_avatar')->default(0);
         $table->string('p_mensaje', 60)->default('');
         $table->string('p_sitio', 255)->default('');
         $table->text('p_socials')->nullable();
         $table->string('p_configs', 100)->default('a:3:{s:1:"m";s:1:"5";s:2:"mf";i:5;s:3:"rmp";s:1:"5";}');
         $table->string('p_total', 54)->default('a:6:{i:0;i:5;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;}');
      });

      Schema::create('u_portal', function(Blueprint $table) {
         $table->integer('user_id')->unsigned()->default(0)->primary();
         $table->text('last_posts_visited')->nullable();
         $table->text('last_posts_shared')->nullable();
         $table->text('last_posts_cats')->nullable();
         $table->text('c_monitor')->nullable();
      });

      Schema::create('u_sessions', function (Blueprint $table) {
         $table->string('session_id', 32)->default('')->primary();
         $table->integer('session_user_id')->unsigned()->default(0);
         $table->string('session_ip', 45)->default('');
         $table->integer('session_time')->unsigned()->default(0);
         $table->tinyInteger('session_autologin')->default(0);

         $table->addIndex('session_user_id');
         $table->addIndex('session_time');
      });

      Schema::create('u_suspension', function (Blueprint $table) {
         $table->id('susp_id');
         $table->integer('user_id')->unsigned()->default(0);
         $table->text('susp_causa')->nullable();
         $table->integer('susp_date')->unsigned()->default(0);
         $table->integer('susp_termina')->unsigned()->default(0);
         $table->integer('susp_mod')->unsigned()->default(0);
         $table->string('susp_ip', 45)->default('');
      });

   }

   public function down(): void {
      Schema::dropIfExists('u_actividad');
      Schema::dropIfExists('u_avisos');
      Schema::dropIfExists('u_bloqueos');
      Schema::dropIfExists('u_follows');
      Schema::dropIfExists('u_miembros');
      Schema::dropIfExists('u_monitor');
      Schema::dropIfExists('u_nicks');
      Schema::dropIfExists('u_perfil');
      Schema::dropIfExists('u_portal');
      Schema::dropIfExists('u_sessions');
      Schema::dropIfExists('u_suspension');
   }
};

