<?php
$server = 'sql.freedb.tech';
$dbname = 'freedb_myschool';
$user = 'freedb_administration';
$password = 'TDRG3v#fSAn$sAW';

try {
  $connection = new PDO("mysql:host=$server;dbname=$dbname", $user, $password);
  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo 'Database connection successful!'; // Mensaje de éxito
} catch (PDOException $th) {
  echo 'Connection error: ' . $th->getMessage(); // Mensaje de error
}
?>
