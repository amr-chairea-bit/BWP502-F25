<?php


$server_name = "localhost";            
$database_name = "student_registration_system"; 
$database_username = "root";            
$database_password = "";            
try {
    $connection = new PDO("mysql:host=$server_name;dbname=$database_name", $database_username, $database_password);

    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 

 catch (PDOException $error)
  {
    echo "عذراً، فشل الاتصال بقاعدة البيانات: " . $error->getMessage();
  }

?>
