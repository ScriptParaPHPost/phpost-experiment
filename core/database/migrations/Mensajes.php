<?php

return new class extends Migration {
   public function up(): void {
      Schema::create('u_mensajes', function(Blueprint $table) {
         $table->id('mp_id');
         $table->integer('mp_to')->unsigned()->default(0);
         $table->integer('mp_from')->unsigned()->default(0);
         $table->tinyInteger('mp_answer')->default(0);
         $table->tinyInteger('mp_read_to')->default(0);
         $table->tinyInteger('mp_read_from')->default(1);
         $table->tinyInteger('mp_read_mon_to')->default(0);
         $table->tinyInteger('mp_read_mon_from')->default(1);
         $table->tinyInteger('mp_del_to')->default(0);
         $table->tinyInteger('mp_del_from')->default(0);
         $table->string('mp_subject', 50)->default('');
         $table->string('mp_preview', 75)->default('');
         $table->integer('mp_date')->unsigned()->default(0);
      });

      Schema::create('u_respuestas', function(Blueprint $table) {
         $table->id('mr_id');
         $table->integer('mp_id')->unsigned()->default(0);
         $table->integer('mr_from')->unsigned()->default(0);
         $table->text('mr_body')->nullable();
         $table->string('mr_ip', 45)->default('');
         $table->integer('mr_date')->unsigned()->default(0);
      });
   }

   public function down(): void {
      Schema::dropIfExists('u_mensajes');
      Schema::dropIfExists('u_respuestas');
   }
};
