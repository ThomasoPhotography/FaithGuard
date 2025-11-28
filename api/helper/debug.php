<?php
  //enable debugging
  error_reporting(E_ALL);
  ini_set("display_errors", "on");

  
  function print_r_pre($var){
    print("<pre>");
    print_r($var);
    print("</pre>");
  }
  
?>