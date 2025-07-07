<?php

return new class extends Migration {

   public function up(): void {

      Schema::create('u_rangos', function(Blueprint $table) {
         $table->id('rango_id');
         $table->string('r_name', 32)->default('');
         $table->char('r_color', 6)->default(171717);
         $table->string('r_image', 32)->default('new.png');
         $table->smallInteger('r_cant')->default(0);
         $table->text('r_allows')->nullable();
         $table->tinyInteger('r_type')->default(0);
      });

      Schema::getPDO()->exec("INSERT INTO `u_rangos` (`rango_id`, `r_name`, `r_color`, `r_image`, `r_cant`, `r_allows`, `r_type`) VALUES
         (1, 'Administrador', 'D6030B', 'Oficial.png', 0, 'a:4:{s:4:\"suad\";s:2:\"on\";s:4:\"goaf\";s:1:\"5\";s:5:\"gopfp\";s:2:\"20\";s:5:\"gopfd\";s:2:\"50\";}', 0),
        	(2, 'Moderador', 'ff9900', 'Moderador.png', 0, 'a:4:{s:4:\"sumo\";s:2:\"on\";s:4:\"goaf\";s:2:\"15\";s:5:\"gopfp\";s:2:\"18\";s:5:\"gopfd\";s:2:\"30\";}', 0),
         (3, 'Novato', '171717', 'Novato.png', 0, 'a:12:{s:4:\"godp\";s:2:\"on\";s:4:\"gopp\";s:2:\"on\";s:5:\"gopcp\";s:2:\"on\";s:5:\"govpp\";s:2:\"on\";s:5:\"govpn\";s:2:\"on\";s:5:\"goepc\";s:2:\"on\";s:5:\"godpc\";s:2:\"on\";s:4:\"gopf\";s:2:\"on\";s:5:\"gopcf\";s:2:\"on\";s:4:\"goaf\";s:2:\"20\";s:5:\"gopfp\";s:1:\"5\";s:5:\"gopfd\";s:1:\"5\";}', 0),
         (4, 'New Full User', '0198E7', 'NewFullUser.png', 50, 'a:12:{s:4:\"godp\";s:2:\"on\";s:4:\"gopp\";s:2:\"on\";s:5:\"gopcp\";s:2:\"on\";s:5:\"govpp\";s:2:\"on\";s:5:\"govpn\";s:2:\"on\";s:5:\"goepc\";s:2:\"on\";s:5:\"godpc\";s:2:\"on\";s:4:\"gopf\";s:2:\"on\";s:5:\"gopcf\";s:2:\"on\";s:4:\"goaf\";s:2:\"20\";s:5:\"gopfp\";s:2:\"10\";s:5:\"gopfd\";s:2:\"10\";}', 1),
         (5, 'Full User', '00ccff', 'FullUser.png', 70, 'a:12:{s:4:\"godp\";s:2:\"on\";s:4:\"gopp\";s:2:\"on\";s:5:\"gopcp\";s:2:\"on\";s:5:\"govpp\";s:2:\"on\";s:5:\"govpn\";s:2:\"on\";s:5:\"goepc\";s:2:\"on\";s:5:\"godpc\";s:2:\"on\";s:4:\"gopf\";s:2:\"on\";s:5:\"gopcf\";s:2:\"on\";s:4:\"goaf\";s:2:\"20\";s:5:\"gopfp\";s:2:\"12\";s:5:\"gopfd\";s:2:\"20\";}', 1),
         (6, 'Great User', '01A021', 'GreatUser.png', 0, 'a:12:{s:4:\"godp\";s:2:\"on\";s:4:\"gopp\";s:2:\"on\";s:5:\"gopcp\";s:2:\"on\";s:5:\"govpp\";s:2:\"on\";s:5:\"govpn\";s:2:\"on\";s:5:\"goepc\";s:2:\"on\";s:5:\"godpc\";s:2:\"on\";s:4:\"gopf\";s:2:\"on\";s:5:\"gopcf\";s:2:\"on\";s:4:\"goaf\";s:2:\"20\";s:5:\"gopfp\";s:2:\"11\";s:5:\"gopfd\";s:2:\"15\";}', 0),
         (7, 'Gold User', 'cc6600', 'GoldUser.png', 120, 'a:12:{s:4:\"godp\";s:2:\"on\";s:4:\"gopp\";s:2:\"on\";s:5:\"gopcp\";s:2:\"on\";s:5:\"govpp\";s:2:\"on\";s:5:\"govpn\";s:2:\"on\";s:5:\"goepc\";s:2:\"on\";s:5:\"godpc\";s:2:\"on\";s:4:\"gopf\";s:2:\"on\";s:5:\"gopcf\";s:2:\"on\";s:4:\"goaf\";s:2:\"20\";s:5:\"gopfp\";s:2:\"12\";s:5:\"gopfd\";s:2:\"25\";}', 1)
      ");
   }

   public function down(): void {
      Schema::dropIfExists('u_rangos');
   }
};
