<?php

return new class extends Migration {

   public function up(): void {
      Schema::create('w_historial', function (Blueprint $table) {
         $table->id('id');
         $table->integer('pofid')->unsigned()->default(0);
         $table->tinyInteger('type')->default(0);
         $table->tinyInteger('action')->default(0);
         $table->integer('mod')->unsigned()->default(0);
         $table->string('reason', 255)->default('');
         $table->integer('date')->unsigned()->default(0);
         $table->string('mod_ip', 45)->default('');
      });
   }

   public function down(): void {
      Schema::dropIfExists('w_historial');
   }
};