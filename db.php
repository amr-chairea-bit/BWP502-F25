<?php


$server_name = "db.fr-roub1.bengt.wasmernet.com";            
$database_name = "student_registration_system"; 
$database_username = "user_94b1aa66";            
$database_password = "pw_hA7zUos50IFxcWs5rRq54qRnwsNlK082";            
try {
    $connection = new PDO("mysql:host=$server_name;dbname=$database_name", $database_username, $database_password);

    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 

 catch (PDOException $error)
  {
    echo "عذراً، فشل الاتصال بقاعدة البيانات: " . $error->getMessage();
  }

?>
