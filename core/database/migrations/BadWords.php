<?php

return new class extends Migration {

	public function up(): void {
		Schema::create('w_badwords', function (Blueprint $table) {
			$table->id('wid');
			$table->string('word', 255)->default('');
			$table->string('swop', 255)->default('');
			$table->tinyInteger('method')->default(0);
			$table->tinyInteger('type')->default(0);
			$table->integer('author')->unsigned()->default(0);
			$table->string('reason', 255)->default('');
			$table->integer('date')->unsigned()->default(0);
		});
	}

	public function down(): void {
		Schema::dropIfExists('w_badwords');
	}
};