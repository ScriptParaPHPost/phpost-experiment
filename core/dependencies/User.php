<?php 

class User {

   public function __construct(

      public readonly tsCore $tsCore,

      public readonly tsSession $tsSession,

      public readonly Junk $Junk,

      public readonly Avatar $Avatar

   ) {}

}