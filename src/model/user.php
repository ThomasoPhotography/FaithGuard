<?php

class User{

    public $id;
    public $username;
    public $password;
    public $created;
    public $admin;
  

    public function __construct($parId=-1, $parUsername="",$parPassword ="", $parCreated ="", $parAdmin = ""){
        $this->id =$parId;
        $this->username = $parUsername;
        $this->password = $parPassword;
        $this->created = $parCreated;
        $this->admin = $parAdmin;
    }
}


?>