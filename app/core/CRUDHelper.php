<?php

class CRUDHelper
{
  private $server = '127.0.0.1:3306';
  private $dbname = 'myschool';
  private $user = 'root';
  private $password = '12345';
  private $connection = null;

  public function __construct()
  {
    $this->connect();
  }

  public function connect()
  {
    try {
      $this->connection = new PDO("mysql:host={$this->server};dbname={$this->dbname}", $this->user, $this->password);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $th) {
      echo 'Connection error: ' . $th->getMessage();
    }
  }

  // Get connection
  public function getConnection()
  {
    return $this->connection;
  }

  // Get specific data
  public function getData($query)
  {
    $QueryStatement = $this->connection->prepare($query);
    $QueryStatement->execute();
    return $QueryStatement->fetchAll(PDO::FETCH_ASSOC);
  }
}
?>