<?php
/**
*   Configuration file for course critique
**/

//Search bar location (user facing)
$rootURL = "http://critique.gatech.edu";

class database {
  //MySQL credentials, app only needs read only access
  private $RDBMType = "mysql";  //In the PHP PDO format
  private $username = "php-critique";
  private $password = "R8zgKgZW3tUNYEUR7qT98xFb";
  private $dbhost   = "web-db1.gatech.edu";
  private $database = "critique_data";
  private $table    = "Data";
  
  function __construct() {
          //Connection string
          $this->pdo = new PDO( $this->RDBMType.":host=".$this->dbhost.";dbname=".$this->database, $this->username, $this->password);
  }


}
?>
