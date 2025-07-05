<?php 

class Afiliado {

   public function __construct(

      public readonly tsCore $tsCore,

      public readonly tsUser $tsUser,

      public readonly Junk $Junk

   ) {}
   
}