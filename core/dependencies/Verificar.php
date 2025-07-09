<?php 

class Verificar {

   public function __construct(
      
      public readonly tsCore $tsCore,
      
      public readonly Junk $Junk,

      public readonly PasswordManager $PasswordManager,

      public readonly Config $Config
      
   ) {}

}