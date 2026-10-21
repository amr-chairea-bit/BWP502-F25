<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام تسجيل الطلاب</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">نظام تسجيل الطلاب</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isset($_SESSION['user_id'])): ?>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">الرئيسية</a>
                </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_student.php">إضافة طالب</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_students.php">عرض الطلاب</a>
                        </li>
                    </ul>
                    <span class="text-white me-3">أهلاً، <?php echo $_SESSION['user_name']; ?></span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">تسجيل الخروج</a>
<?php else: ?>
                    <div class="ms-auto">
                        <a href="login.php" class="btn btn-outline-light btn-sm me-2">تسجيل الدخول</a>
                        <a href="register.php" class="btn btn-light btn-sm">حساب جديد</a>
                    </div>
<?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        