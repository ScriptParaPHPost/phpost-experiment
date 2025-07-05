<?php 

class Authentication {

   public function __construct(
      
      public readonly tsCore $tsCore,
      
      public readonly tsUser $tsUser,
      
      public readonly Junk $Junk,

      public readonly PasswordManager $PasswordManager,

      public readonly tsSession $tsSession
      
   ) {}

}