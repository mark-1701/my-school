<?php
$server = '127.0.0.1:3306';
$dbname = 'myschool';
$user = 'root';
$password = '12345';

try {
  $connection = new PDO("mysql:host=$server;dbname=$dbname", $user, $password);
  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $th) {
  echo 'Connection error: ' . $th->getMessage();
}
?>