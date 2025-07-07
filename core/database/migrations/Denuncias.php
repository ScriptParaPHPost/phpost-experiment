<?php

return new class extends Migration {

   public function up(): void {
      Schema::create('w_denuncias', function (Blueprint $table) {
         $table->id('did');
         $table->integer('obj_id')->unsigned()->default(0);
         $table->integer('d_user')->unsigned()->default(0);
         $table->smallInteger('d_razon')->default(0);
         $table->text('d_extra')->nullable();
         $table->tinyInteger('d_total')->default(1);
         $table->tinyInteger('d_type')->default(0);
         $table->integer('d_date')->unsigned()->default(0);
      });
   }

   public function down(): void {
      Schema::dropIfExists('w_denuncias');
   }
};