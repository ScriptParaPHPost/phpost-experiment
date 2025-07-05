<?php 

class Registro {

   public function __construct(
      
      public readonly tsCore $tsCore,
      
      public readonly tsAuthentication $tsAuthentication,
      
      public readonly Junk $Junk,

      public readonly PasswordManager $PasswordManager,

      public readonly Avatar $Avatar
      
   ) {}

}