<?php

return new class extends Migration {
   public function up(): void {
      // Tabla principal del muro
      Schema::create('u_muro', function(Blueprint $table) {
         $table->id('pub_id');
         $table->integer('p_user')->unsigned()->default(0);
         $table->integer('p_user_pub')->unsigned()->default(0);
         $table->integer('p_date')->unsigned()->default(0);
         $table->text('p_body')->nullable();
         $table->integer('p_likes')->unsigned()->default(0);
         $table->integer('p_favorites')->unsigned()->default(0);
         $table->integer('p_shared')->unsigned()->default(0);
         $table->integer('p_comments')->unsigned()->default(0);
         $table->tinyInteger('p_type')->default(0);
         $table->tinyInteger('p_privacity')->default(0);
         $table->string('p_ip', 45)->default('');
      });

      // Adjuntos de publicaciones del muro
      Schema::create('u_muro_adjuntos', function(Blueprint $table) {
         $table->id('adj_id');
         $table->integer('pub_id')->unsigned()->default(0);
         $table->string('adj_title', 100)->default('');
         $table->string('adj_url', 255)->default('');
         $table->string('adj_image', 255)->default('');
         $table->text('adj_description');
      });

      // Comentarios del muro
      Schema::create('u_muro_comentarios', function(Blueprint $table) {
         $table->id('cid');
         $table->integer('pub_id')->unsigned()->default(0);
         $table->integer('c_user')->unsigned()->default(0);
         $table->integer('c_date')->unsigned()->default(0);
         $table->text('c_body')->nullable();
         $table->integer('c_likes')->unsigned()->default(0);
         $table->string('c_ip', 45)->default('');
      });

      // Info adicional (likes, compartidos, favoritos)
      Schema::create('u_muro_info', function(Blueprint $table) {
         $table->id('iid');
         $table->integer('pub_id')->unsigned()->default(0);
         $table->integer('user_id')->unsigned()->default(0);
         $table->tinyInteger('obj_type')->default(0);
         $table->enum(['shared','favorites','likes'], 'tipo');
         $table->integer('date')->unsigned()->default(0);
      });

      // Tags en el muro
      Schema::create('u_muro_tags', function(Blueprint $table) {
         $table->id('tag_id');
         $table->integer('obj_id')->unsigned()->default(0);
         $table->string('tag_text', 50)->default('');
         $table->integer('date')->unsigned()->default(0);
      });

      // Pins del usuario
      Schema::create('u_pins', function(Blueprint $table) {
         $table->id('p_id');
         $table->integer('p_user')->unsigned()->default(0);
         $table->string('p_data', 50)->default('');
         $table->integer('date')->unsigned()->default(0);
      });
   }

   public function down(): void {
      Schema::dropIfExists('u_muro');
      Schema::dropIfExists('u_muro_adjuntos');
      Schema::dropIfExists('u_muro_comentarios');
      Schema::dropIfExists('u_muro_info');
      Schema::dropIfExists('u_muro_tags');
      Schema::dropIfExists('u_pins');
   }
};
