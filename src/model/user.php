<?php

class User{

    public $id;
    public $login;
    public $paswoord;
  

    public function __construct($parId=-1, $parLogin="",$parPaswoord =""){
        $this->id =$parId;
        $this->login = $parLogin;
        $this->paswoord = $parPaswoord;
    }
}


?>