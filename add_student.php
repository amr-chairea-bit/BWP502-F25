<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_name = trim($_POST['name']);
    $student_email = trim($_POST['email']);
    $student_phone = trim($_POST['phone']);
    $student_address = trim($_POST['address']);

    if (empty($student_name) || empty($student_email) || empty($student_phone) || empty($student_address)) {
        $error_message = "الرجاء تعبئة جميع الحقول.";
    } 
    elseif (!filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "صيغة البريد الإلكتروني غير صحيحة.";
    } 
    else {

        $profile_picture_path = NULL;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {

            $upload_folder = "uploads/";
            $file_name = time() . "_" . basename($_FILES['profile_picture']['name']);
            $target_file = $upload_folder . $file_name;
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($image_file_type, $allowed_types)) {
                $error_message = "يُسمح فقط برفع صور بصيغة JPG, JPEG, PNG, GIF.";
            } 
            elseif ($_FILES['profile_picture']['size'] > 2000000) {
                $error_message = "حجم الصورة كبير جداً (الحد الأقصى 2 ميجابايت).";
            } 
            elseif (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                $profile_picture_path = $target_file;
            } 
            else {
                $error_message = "حدث خطأ أثناء رفع الصورة.";
            }
        }

        if (empty($error_message)) {

            $insert_query = "INSERT INTO students (name, email, phone, address, profile_picture) 
                             VALUES (:name, :email, :phone, :address, :profile_picture)";
            $insert_statement = $connection->prepare($insert_query);
            $insert_statement->bindParam(':name', $student_name);
            $insert_statement->bindParam(':email', $student_email);
            $insert_statement->bindParam(':phone', $student_phone);
            $insert_statement->bindParam(':address', $student_address);
            $insert_statement->bindParam(':profile_picture', $profile_picture_path);

            if ($insert_statement->execute()) {
                $success_message = "تم إضافة الطالب بنجاح!";
            } 
            else {
                $error_message = "حدث خطأ أثناء إضافة الطالب.";
            }
        }
    }
}

include __DIR__ . '/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white text-center">
                <h3>إضافة طالب جديد</h3>
            </div>
            <div class="card-body">

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">اسم الطالب</label>
                        <input type="text" name="name" class="form-control" placeholder="ادخل اسم الطالب" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم الهاتف</label>
                        <input type="text" name="phone" class="form-control" placeholder="09XXXXXXXX" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="ادخل عنوان الطالب" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الصورة الشخصية (اختياري)</label>
                        <input type="file" name="profile_picture" class="form-control" accept="image/*">
                    </div>
                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-success">إضافة الطالب</button>
                        <a href="view_students.php" class="btn btn-secondary">عرض الطلاب</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
