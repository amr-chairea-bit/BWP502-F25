<?php
session_start();





if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view_students.php");
    exit();
}
$student_id = (int)$_GET['id'];
$select_query = "SELECT profile_picture FROM students WHERE id = :id";
$select_statement = $connection->prepare($select_query);
$select_statement->bindParam(':id', $student_id);
$select_statement->execute();
$student = $select_statement->fetch(PDO::FETCH_ASSOC);
if (!$student) {
    header("Location: view_students.php");
    exit();
}
if (!empty($student['profile_picture']) && file_exists($student['profile_picture'])) {
    unlink($student['profile_picture']);
}





$delete_query = "DELETE FROM students WHERE id = :id";
$delete_statement = $connection->prepare($delete_query);
$delete_statement->bindParam(':id', $student_id);



if ($delete_statement->execute())
   {
    header("Location: view_students.php");
    exit();
   }
    else { echo "حدث خطأ أثناء حذف الطالب."; }
?>
