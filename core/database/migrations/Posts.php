<?php 

return new class extends Migration {

   public function up(): void {
      Schema::create('p_borradores', function(Blueprint $table) {
         $table->id('bid');
         $table->integer('b_user')->unsigned()->default(0);
         $table->integer('b_date')->unsigned()->default(0);
         $table->string('b_title', 120)->default('');
         $table->text('b_body')->nullable();
         $table->string('b_portada', 255)->default('');
         $table->string('b_tags', 128)->default('');
         $table->integer('b_category')->unsigned()->default(0);
         $table->tinyInteger('b_private')->default(0);
         $table->tinyInteger('b_block_comments')->default(0);
         $table->tinyInteger('b_sponsored')->default(0);
         $table->tinyInteger('b_sticky')->default(0);
         $table->tinyInteger('b_smileys')->default(0);
         $table->tinyInteger('b_visitantes')->default(0);
         $table->integer('b_post_id')->unsigned()->default(0);
         $table->tinyInteger('b_status')->default(1);
         $table->string('b_causa', 128)->default('');
         $table->addIndex('b_user');
         $table->addIndex('b_status');
         $table->addIndex('b_post_id');
      });

      Schema::create('p_comentarios', function(Blueprint $table) {
         $table->id('cid');
         $table->integer('c_post_id')->unsigned()->default(0);
         $table->integer('c_user')->unsigned()->default(0);
         $table->integer('c_date')->unsigned()->default(0);
         $table->text('c_body')->nullable();
         $table->smallInteger('c_votos')->unsigned()->default(0);
         $table->tinyInteger('c_status')->default(0);
         $table->string('c_ip', 45)->default('');
      });

      Schema::create('p_favoritos', function(Blueprint $table) {
         $table->id('fav_id');
         $table->integer('fav_user')->unsigned()->default(0);
         $table->integer('fav_post_id')->unsigned()->default(0);
         $table->integer('fav_date')->unsigned()->default(0);
      });

      Schema::create('p_posts', function(Blueprint $table) {
         $table->id('post_id');
         $table->integer('post_user')->unsigned()->default(0);
         $table->integer('post_category')->unsigned()->default(0);
         $table->string('post_title', 120)->default('');
         $table->text('post_body')->nullable();
         $table->string('post_portada', 255)->default('');
         $table->integer('post_date')->unsigned()->default(0);
         $table->string('post_tags', 128)->default('');
         $table->integer('post_puntos')->unsigned()->default(0);
         $table->integer('post_comments')->unsigned()->default(0);
         $table->integer('post_seguidores')->unsigned()->default(0);
         $table->integer('post_shared')->unsigned()->default(0);
         $table->integer('post_favoritos')->unsigned()->default(0);
         $table->integer('post_cache')->unsigned()->default(0);
         $table->integer('post_hits')->unsigned()->default(0);
         $table->string('post_ip', 45)->default('');
         $table->tinyInteger('post_private')->default(0);
         $table->tinyInteger('post_block_comments')->default(0);
         $table->tinyInteger('post_sponsored')->default(0);
         $table->tinyInteger('post_sticky')->default(0);
         $table->tinyInteger('post_smileys')->default(0);
         $table->tinyInteger('post_visitantes')->default(0);
         $table->tinyInteger('post_status')->default(0);
         $table->addIndex('post_user');
         $table->addIndex('post_status');
         $table->addIndex('post_category');
         $table->addIndex('post_date');
         // Si más adelante querés soporte para FULLTEXT, podrías implementarlo aquí manualmente
      });

      Schema::create('p_votos', function(Blueprint $table) {
         $table->id('voto_id');
         $table->integer('tid')->unsigned()->default(0);
         $table->integer('tuser')->unsigned()->default(0);
         $table->integer('cant')->unsigned()->default(0);
         $table->tinyInteger('type')->default(1);
         $table->integer('date')->unsigned()->default(0);
      });
   }

   public function down(): void {
      Schema::dropIfExists('p_borradores');
      Schema::dropIfExists('p_comentarios');
      Schema::dropIfExists('p_favoritos');
      Schema::dropIfExists('p_posts');
      Schema::dropIfExists('p_votos');
   }

};