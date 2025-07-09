<?php

/**
 * PHPost 2025
 * 
 * Clase PageBootstrap
 * Encapsula la lógica repetitiva de configuración de páginas
 * de PHPost (título, nivel, ajax, permisos, etc.)
 * 
 * @author      PHPost Team
 * @copyright   2025
 * @link        http://github.com/isidromlc/PHPost
 * @link        http://github.com/joelmiguelvalente
 * @link        https://www.linkedin.com/in/joelmiguelvalente/
*/

class PageBootstrap {

   public string $page;

   public int $ajax;

   public string $title;

   public bool $continue;

   protected tsCore $tsCore;

   protected tsSmarty $tsSmarty;

   public function __construct(string $page, int $level, tsCore $tsCore, tsSmarty $tsSmarty) {
      if (!$tsCore || !$tsSmarty) {
         throw new InvalidArgumentException('Todas las dependencias son obligatorias en PageBootstrap.');
      }
      $this->tsCore = $tsCore;
      $this->tsSmarty = $tsSmarty;

      $this->page = $page;
      $this->ajax = empty($_GET['ajax']) ? 0 : 1;
      $this->title = $this->tsCore->settings['titulo'];
      $this->continue = true;

      $this->validateAccess($level);
   }

   /**
    * Verifica el nivel de acceso requerido
    */
   protected function validateAccess(int $level): void {
      $message = $this->tsCore->setLevel($level, true);
      if (!$message) {
         $this->page = 'aviso';
         $this->ajax = 0;
         $this->continue = false;
         $this->tsSmarty->assign("tsAviso", $message);
      }
   }

   /**
    * Asignar variables comunes al template
    */
   public function assignDefaults(): void {
      $this->tsSmarty->assign("tsTitle", $this->title());
      $this->tsSmarty->assign("tsPage", $this->page());
   }

   public function title(): string {
      return $this->title;
   }

   public function page(): string {
      return $this->page;
   }

   public function ajax(): bool {
      return $this->ajax;
   }

   /**
    * ¿Se puede continuar con la ejecución?
    */
   public function canContinue(): bool {
      return $this->continue;
   }
} 