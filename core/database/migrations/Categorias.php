<?php 

// database/migrations/p_categorias.php

return new class extends Migration {

   public function up(): void {
      Schema::create('p_categorias', function(Blueprint $table) {
         $table->id('cid');
         $table->integer('c_orden')->unsigned()->default(0);
         $table->string('c_nombre', 40)->default('');
         $table->string('c_seo', 40)->default('');
         $table->string('c_img', 40)->default('comments.png');
      });

      Schema::getPDO()->exec("INSERT INTO `p_categorias` (`cid`, `c_orden`, `c_nombre`, `c_seo`, `c_img`) VALUES
      (1, 1, 'Animaciones', 'animaciones', 'flash.png'),
      (2, 2, 'Apuntes y Monografías', 'apuntesymonografias', 'report.png'),
      (3, 3, 'Arte', 'arte', 'palette.png'),
      (4, 4, 'Autos y Motos', 'autosymotos', 'car.png'),
      (5, 5, 'Celulares', 'celulares', 'phone.png'),
      (6, 6, 'Ciencia y Educación', 'cienciayeducacion', 'lab.png'),
      (7, 7, 'Comics', 'comics', 'comic.png'),
      (8, 8, 'Deportes', 'deportes', 'sport.png'),
      (9, 9, 'Downloads', 'downloads', 'disk.png'),
      (10, 10, 'E-books y Tutoriales', 'ebooksytutoriales', 'ebook.png'),
      (11, 11, 'Ecología', 'ecologia', 'nature.png'),
      (12, 12, 'Economía y Negocios', 'economiaynegocios', 'economy.png'),
      (13, 13, 'Femme', 'femme', 'female.png'),
      (14, 14, 'Hazlo tu mismo', 'hazlotumismo', 'escuadra.png'),
      (15, 15, 'Humor', 'humor', 'humor.png'),
      (16, 16, 'Imágenes', 'imagenes', 'photo.png'),
      (17, 17, 'Info', 'info', 'book.png'),
      (18, 18, 'Juegos', 'juegos', 'controller.png'),
      (19, 19, 'Links', 'links', 'link.png'),
      (20, 20, 'Linux', 'linux', 'tux.png'),
      (21, 21, 'Mac', 'mac', 'mac.png'),
      (22, 22, 'Manga y Anime', 'mangayanime', 'manga.png'),
      (23, 23, 'Mascotas', 'mascotas', 'pet.png'),
      (24, 24, 'Música', 'musica', 'music.png'),
      (25, 25, 'Noticias', 'noticias', 'newspaper.png'),
      (26, 26, 'Off Topic', 'offtopic', 'comments.png'),
      (27, 27, 'Recetas y Cocina', 'recetasycocina', 'cake.png'),
      (28, 28, 'Salud y Bienestar', 'saludybienestar', 'heart.png'),
      (29, 29, 'Solidaridad', 'solidaridad', 'salva.png'),
      (30, 30, 'Taringa!', 'taringa', 'tscript.png'),
      (31, 31, 'Turismo', 'turismo', 'brujula.png'),
      (32, 32, 'TV, Peliculas y series', 'tvpeliculasyseries', 'tv.png'),
      (33, 33, 'Videos On-line', 'videosonline', 'film.png')");
   }

   public function down(): void {
      Schema::dropIfExists('p_categorias');
   }
};