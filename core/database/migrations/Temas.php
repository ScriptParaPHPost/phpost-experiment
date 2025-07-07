<?php

return new class extends Migration {

   public function up(): void {
      Schema::create('w_temas', function (Blueprint $table) {
         $table->id('tid');
         $table->string('t_name', 80)->default('');
         $table->string('t_url', 255)->default('');
         $table->string('t_path', 30)->default('');
         $table->string('t_copy', 120)->default('');
      });
   }

   public function down(): void {
      Schema::dropIfExists('w_temas');
   }
};