<?php

class User
{
    public $user_id;
    public $username;
    public $password;
    public $created_at;
    public $is_admin;

    public function __construct($parUserId = -1, $parUsername = "", $parPassword = "", $parCreatedAt = "", $parIsAdmin = false)
    {
        $this->user_id = $parUserId;
        $this->username = $parUsername;
        $this->password = $parPassword;
        $this->created_at = $parCreatedAt;
        $this->is_admin = $parIsAdmin;
    }
}
?>