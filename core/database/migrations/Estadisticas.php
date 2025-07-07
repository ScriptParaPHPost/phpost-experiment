<?php
// database/migrations/w_stats.php

return new class extends Migration {

	public function up(): void {
		Schema::create('w_stats', function (Blueprint $table) {
			$table->tinyInteger('stats_no')->default(0)->primary();
			$table->integer('stats_max_online')->unsigned()->default(0);
			$table->integer('stats_max_time')->unsigned()->default(0);
			$table->integer('stats_time')->unsigned()->default(0);
			$table->integer('stats_time_cache')->unsigned()->default(0);
			$table->integer('stats_time_foundation')->unsigned()->default(0);
			$table->integer('stats_time_upgrade')->unsigned()->default(0);
			$table->integer('stats_miembros')->unsigned()->default(0);
			$table->integer('stats_posts')->unsigned()->default(0);
			$table->integer('stats_fotos')->unsigned()->default(0);
			$table->integer('stats_comments')->unsigned()->default(0);
			$table->integer('stats_foto_comments')->unsigned()->default(0);
		});
      Schema::getPDO()->exec("INSERT INTO `w_stats` (`stats_no`, `stats_max_online`) VALUES (1, 0);");
      
	}

	public function down(): void {
		Schema::dropIfExists('w_stats');
	}
};
