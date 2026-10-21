<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>  

<?php include __DIR__ . '/header.php'; ?>

<div class="row justify-content-center">
<div class="col-md-8">
<div class="card shadow">
<div class="card-body text-center">
<h2 class="mb-4">مرحباً بك، <?php echo $_SESSION['user_name']; ?>!</h2>
<p class="lead">لقد تم تسجيل دخولك بنجاح إلى نظام تسجيل الطلاب.</p>
<hr>
<div class="d-grid gap-2 d-md-block mt-4">
<a href="add_student.php" class="btn btn-success m-2">إضافة طالب جديد</a>
<a href="view_students.php" class="btn btn-info m-2">عرض الطلاب</a>
</div>
</div>
</div>
</div>
</div>

<?php include __DIR__ . '/footer.php'; ?>

