<?php 

return new class extends Migration {

   public function up(): void {

      Schema::create('f_fotos', function(Blueprint $table) {
         $table->id('foto_id');
         $table->string('f_title', 40)->default('');
         $table->text('f_description')->nullable();
         $table->tinyText('f_url')->nullable();
         $table->integer('f_user')->unsigned()->default(0);
         $table->tinyInteger('f_closed')->default(0);
         $table->tinyInteger('f_visitas')->default(0);
         $table->tinyInteger('f_status')->default(0);
         $table->tinyInteger('f_last')->default(0);
         $table->integer('f_hits')->unsigned()->default(0);
         $table->string('f_ip', 45)->default('');
         $table->integer('f_date')->unsigned()->default(0);
         $table->addIndex('f_user');
         $table->addIndex('f_status');
         $table->addIndex(['f_user', 'f_status']);
      });

      Schema::create('f_comentarios', function(Blueprint $table) {
         $table->id('cid');
         $table->integer('c_foto_id')->unsigned()->default(0);
         $table->integer('c_user')->unsigned()->default(0);
         $table->integer('c_date')->unsigned()->default(0);
         $table->text('c_body')->nullable();
         $table->string('c_ip', 45)->default('');
         $table->addIndex('c_user');
      });

      Schema::create('f_votos', function(Blueprint $table) {
         $table->id('vid');
         $table->integer('v_foto_id')->unsigned()->default(0);
         $table->integer('v_user')->unsigned()->default(0);
         $table->tinyInteger('v_type')->default(0);
         $table->integer('v_date')->unsigned()->default(0);
         $table->string('c_ip', 45)->default('');
         $table->addIndex('v_foto_id');
         $table->addIndex('v_user');
         $table->addIndex(['v_foto_id', 'v_user']);
      });

   }

   public function down(): void {
      Schema::dropIfExists('f_fotos');
      Schema::dropIfExists('f_comentarios');
      Schema::dropIfExists('f_votos');
   }

};