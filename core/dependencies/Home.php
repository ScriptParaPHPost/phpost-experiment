<?php 

class Home {

   public function __construct(
      
      public readonly tsCore $tsCore,
      
      public readonly tsUser $tsUser,
      
      public readonly Paginator $Paginator,
      
      public readonly Junk $Junk
      
   ) {}

}