<?php
require_once 'db.php';

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_username = trim($_POST['username']);
    $user_email = trim($_POST['email']);
    $user_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($user_username) || empty($user_email) || empty($user_password) || empty($confirm_password)) {
        $error_message = "الرجاء تعبئة جميع الحقول.";
    } elseif ($user_password !== $confirm_password) {
        $error_message = "كلمة المرور وتأكيد كلمة المرور غير متطابقين.";
    } else {
        $check_user_query = "SELECT id FROM users WHERE email = :email OR username = :username";
        $check_statement = $connection->prepare($check_user_query);
        $check_statement->bindParam(':email', $user_email);
        $check_statement->bindParam(':username', $user_username);
        $check_statement->execute();

        if ($check_statement->rowCount() > 0) {
            $error_message = "عذراً، اسم المستخدم أو البريد الإلكتروني مسجل مسبقاً.";
        } else {
            $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

            $insert_user_query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $insert_statement = $connection->prepare($insert_user_query);
            $insert_statement->bindParam(':username', $user_username);
            $insert_statement->bindParam(':email', $user_email);
            $insert_statement->bindParam(':password', $hashed_password);

            if ($insert_statement->execute()) {
                $success_message = "تم إنشاء حسابك بنجاح! يمكنك الآن الانتقال لصفحة تسجيل الدخول.";
            } else {
                $error_message = "حدث خطأ غير متوقع أثناء إنشاء الحساب. حاول مرة أخرى.";
            }
        }
    }
}

include __DIR__ . '/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>إنشاء حساب جديد</h3>
            </div>
            <div class="card-body">
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">اسم المستخدم</label>
                        <input type="text" name="username" class="form-control" placeholder="ادخل اسم المستخدم" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password_field" class="form-control" placeholder="********" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="var passField = document.getElementById('password_field'); if (passField.type === 'password') { passField.type = 'text'; this.textContent = 'إخفاء'; } else { passField.type = 'password'; this.textContent = 'إظهار'; }">
                                إظهار
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تأكيد كلمة المرور</label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" id="confirm_password_field" class="form-control" placeholder="********" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="var passField = document.getElementById('confirm_password_field'); if (passField.type === 'password') { passField.type = 'text'; this.textContent = 'إخفاء'; } else { passField.type = 'password'; this.textContent = 'إظهار'; }">
                                إظهار
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">تسجيل الحساب</button>
                </form>

                <div class="mt-3 text-center">
                    <p>لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>