<?php

return new class extends Migration {

   public function up(): void {
      Schema::create('w_medallas', function(Blueprint $table) {
         $table->id('medal_id');
         $table->integer('m_autor')->unsigned()->default(0);
         $table->string('m_title', 35)->default('');
         $table->string('m_description', 120)->default('');
         $table->string('m_image', 120)->default('');
         $table->integer('m_cant')->unsigned()->default(0);
         $table->tinyInteger('m_type')->default(0);
         $table->integer('m_cond_user')->unsigned()->default(0);
         $table->integer('m_cond_user_rango')->unsigned()->default(0);
         $table->integer('m_cond_post')->unsigned()->default(0);
         $table->integer('m_cond_foto')->unsigned()->default(0);
         $table->integer('m_date')->unsigned()->default(0);
         $table->integer('m_total')->unsigned()->default(0);
      });

      Schema::create('w_medallas_assign', function(Blueprint $table) {
         $table->id('id');
         $table->integer('medal_id')->unsigned()->default(0);
         $table->integer('medal_for')->unsigned()->default(0);
         $table->integer('medal_date')->unsigned()->default(0);
         $table->string('medal_ip', 45)->default('');
      });
   }

   public function down(): void {
      Schema::dropIfExists('w_medallas');
      Schema::dropIfExists('w_medallas_assign');
   }

};