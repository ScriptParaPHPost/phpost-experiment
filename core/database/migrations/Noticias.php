<?php

return new class extends Migration {

	public function up(): void {
		Schema::create('w_noticias', function (Blueprint $table) {
			$table->id('not_id');
			$table->string('not_body', 255)->default('');
			$table->integer('not_autor')->unsigned()->default(0);
			$table->integer('not_date')->unsigned()->default(0);
			$table->tinyInteger('not_active')->default(0);
		});
	}

	public function down(): void {
		Schema::dropIfExists('w_noticias');
	}
};