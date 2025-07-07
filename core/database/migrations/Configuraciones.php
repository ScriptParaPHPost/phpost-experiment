<?php

return new class extends Migration {

    public function up(): void {

      Schema::create('w_config_general', function(Blueprint $table) {
         $table->integer('tscript_id')->unsigned()->default(0)->primary();
         $table->string('titulo', 24)->default('');
         $table->string('slogan', 32)->default('');
         $table->string('url', 255)->default('');
         $table->string('email', 60)->default('');
         $table->string('banner', 100)->default('');
         $table->integer('tema_id')->unsigned()->default(1);
      });
      Schema::getPDO()->exec("INSERT INTO `w_config_general` (`tscript_id`) VALUES (1);");

      Schema::create('w_config_ads', function(Blueprint $table) {
         $table->integer('tscript_id')->unsigned()->primary();
         $table->text('ads_300')->nullable();
         $table->text('ads_468')->nullable();
         $table->text('ads_160')->nullable();
         $table->text('ads_728')->nullable();
         $table->string('ads_search', 50)->default('');
      });
      Schema::getPDO()->exec("INSERT INTO `w_config_ads` (`tscript_id`) VALUES (1);");

      Schema::create('w_config_users', function(Blueprint $table) {
         $table->integer('tscript_id')->unsigned()->primary();
         $table->smallInteger('c_last_active')->default(0);
         $table->tinyInteger('c_allow_sess_ip')->default(1);
         $table->tinyInteger('c_count_guests')->default(0);
         $table->tinyInteger('c_reg_active')->default(1);
         $table->tinyInteger('c_reg_activate')->default(1);
         $table->integer('c_reg_rango', false, true)->default(3);
         $table->tinyInteger('c_met_welcome')->default(0);
         $table->string('c_message_welcome', 500)->default('Hola {{usuario}}, {{bienvenida}} a [b]{{sitio}}[/b].');
         $table->integer('c_fotos_private')->unsigned()->default(0);
         $table->tinyInteger('c_hits_guest')->default(0);
         $table->tinyInteger('c_keep_points')->default(0);
         $table->integer('c_allow_points')->unsigned()->default(0);
         $table->integer('c_allow_edad')->unsigned()->default(16);
         $table->integer('c_allow_sump')->unsigned()->default(0);
         $table->tinyInteger('c_allow_firma')->default(1);
         $table->tinyInteger('c_allow_upload')->default(0);
         $table->tinyInteger('c_allow_portal')->default(1);
         $table->tinyInteger('c_allow_live')->default(1);
         $table->tinyInteger('c_see_mod')->default(0);
         $table->smallInteger('c_stats_cache')->unsigned()->default(15);
         $table->tinyInteger('c_desapprove_post')->default(0);
      });
      Schema::getPDO()->exec("INSERT INTO `w_config_users` (`tscript_id`) VALUES (1);");
        
      Schema::create('w_config_limits', function(Blueprint $table) {
         $table->integer('tscript_id')->unsigned()->primary();
         $table->smallInteger('c_max_posts')->default(50);
         $table->smallInteger('c_max_com')->default(50);
         $table->smallInteger('c_max_nots')->default(99);
         $table->smallInteger('c_max_acts')->default(99);
         $table->integer('c_newr_type')->unsigned()->default(0);
      });
      Schema::getPDO()->exec("INSERT INTO `w_config_limits` (`tscript_id`) VALUES (1);");

      Schema::create('w_config_misc', function(Blueprint $table) {
         $table->integer('tscript_id')->unsigned()->primary();
         $table->tinyInteger('offline')->default(0);
         $table->string('offline_message', 255)->default('Estamos en mantenimiento');
         $table->string('pkey', 70)->default('');
         $table->string('skey', 70)->default('');
         $table->string('version', 26)->default('');
         $table->string('version_code', 26)->default('');
      });
      Schema::getPDO()->exec("INSERT INTO `w_config_misc` (`tscript_id`) VALUES (1);");

   }

   public function down(): void {
      Schema::dropIfExists('w_config_general');
      Schema::dropIfExists('w_config_ads');
      Schema::dropIfExists('w_config_users');
      Schema::dropIfExists('w_config_limits');
      Schema::dropIfExists('w_config_misc');
   }

};