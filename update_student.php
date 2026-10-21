<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

$error_message = "";
$success_message = "";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view_students.php");
    exit();
}

$student_id = (int)$_GET['id'];

$select_query = "SELECT * FROM students WHERE id = :id";
$select_statement = $connection->prepare($select_query);
$select_statement->bindParam(':id', $student_id);
$select_statement->execute();
$student = $select_statement->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header("Location: view_students.php");
    exit();
}

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

        $profile_picture_path = $student['profile_picture'];

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
                if (!empty($student['profile_picture']) && file_exists($student['profile_picture'])) {
                    unlink($student['profile_picture']);
                }
                $profile_picture_path = $target_file;
            } 
            else {
                $error_message = "حدث خطأ أثناء رفع الصورة.";
            }
        }

        if (empty($error_message)) {

            $update_query = "UPDATE students SET name = :name, email = :email, phone = :phone, address = :address, profile_picture = :profile_picture WHERE id = :id";
            $update_statement = $connection->prepare($update_query);
            $update_statement->bindParam(':name', $student_name);
            $update_statement->bindParam(':email', $student_email);
            $update_statement->bindParam(':phone', $student_phone);
            $update_statement->bindParam(':address', $student_address);
            $update_statement->bindParam(':profile_picture', $profile_picture_path);
            $update_statement->bindParam(':id', $student_id);

            if ($update_statement->execute()) {
                $success_message = "تم تحديث بيانات الطالب بنجاح!";
                $student['name'] = $student_name;
                $student['email'] = $student_email;
                $student['phone'] = $student_phone;
                $student['address'] = $student_address;
                $student['profile_picture'] = $profile_picture_path;
            } 
            else {
                $error_message = "حدث خطأ أثناء تحديث البيانات.";
            }
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark text-center">
                <h3>تعديل بيانات الطالب</h3>
            </div>
            <div class="card-body">

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <?php if (!empty($student['profile_picture']) && file_exists($student['profile_picture'])): ?>
                    <div class="text-center mb-3">
                        <img src="<?php echo $student['profile_picture']; ?>" width="100" height="100" style="object-fit: cover; border-radius: 50%;">
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">اسم الطالب</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم الهاتف</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان</label>
                        <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($student['address']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الصورة الشخصية (اتركها فاضية للإبقاء على الصورة الحالية)</label>
                        <input type="file" name="profile_picture" class="form-control" accept="image/*">
                    </div>
                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-warning">حفظ التعديلات</button>
                        <a href="view_students.php" class="btn btn-secondary">إلغاء</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
