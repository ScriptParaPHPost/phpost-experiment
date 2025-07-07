<?php

return new class extends Migration {

   public function up(): void {
      Schema::create('w_afiliados', function(Blueprint $table) {
         $table->id('aid');
         $table->string('a_titulo', 35)->default('');
         $table->string('a_url', 40)->default('');
         $table->string('a_banner', 100)->default('');
         $table->string('a_descripcion', 200)->default('');
         $table->integer('a_sid')->unsigned()->default(0);
         $table->integer('a_hits_in')->unsigned()->default(0);
         $table->integer('a_hits_out')->unsigned()->default(0);
         $table->integer('a_date')->unsigned()->default(0);
         $table->tinyInteger('a_active')->default(0);
      });
   }

   public function down(): void {
      Schema::dropIfExists('w_afiliados');
   }

};