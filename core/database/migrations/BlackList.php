<?php

return new class extends Migration {

   public function up(): void {
     	Schema::create('w_blacklist', function (Blueprint $table) {
     	   $table->id('id');
     	   $table->tinyInteger('type')->default(0);
     	   $table->string('value', 50)->default('');
     	   $table->string('reason', 120)->default('');
     	   $table->integer('author')->unsigned()->default(0);
     	   $table->integer('date')->unsigned()->default(0);
     	});
   }

   public function down(): void {
      Schema::dropIfExists('w_blacklist');
   }

};