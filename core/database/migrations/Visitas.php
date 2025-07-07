<?php

return new class extends Migration {

	public function up(): void {
		Schema::create('w_visitas', function (Blueprint $table) {
			$table->id('id');
			$table->integer('user')->unsigned()->default(0);
			$table->integer('for')->unsigned()->default(0);
			$table->tinyInteger('type')->default(0);
			$table->integer('date')->unsigned()->default(0);
			$table->string('ip', 45)->default('');
			
			$table->addIndex('for');
			$table->addIndex('type');
			$table->addIndex('user');
			$table->addIndex(['for', 'type', 'user']);
		});
	}

	public function down(): void {
		Schema::dropIfExists('w_visitas');
	}
};
