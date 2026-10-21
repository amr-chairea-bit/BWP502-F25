<?php
session_start();
require_once 'db.php';

$error_message = "";

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = trim($_POST['login_input']);
    $user_password = $_POST['password'];

    if (empty($login_input) || empty($user_password)) {
        $error_message = "الرجاء تعبئة جميع الحقول.";
    } else {
        $login_query = "SELECT * FROM users WHERE email = :login_input OR username = :login_input";
        $login_statement = $connection->prepare($login_query);
        $login_statement->bindParam(':login_input', $login_input);
        $login_statement->execute();

        $user_data = $login_statement->fetch(PDO::FETCH_ASSOC);

        if ($user_data && password_verify($user_password, $user_data['password'])) {
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['user_name'] = $user_data['username'];
            $_SESSION['user_email'] = $user_data['email'];

            header("Location: index.php");
            exit();
        } else {
            $error_message = "اسم المستخدم/البريد الإلكتروني أو كلمة المرور غير صحيحة.";
        }
    }
}

include __DIR__ . '/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
<div class="card-header bg-primary text-white text-center">
<h3>تسجيل الدخول</h3>
</div>
<div class="card-body">

<?php if (!empty($error_message)): ?>
<div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
<?php endif; ?>

<form method="POST" action="">
<div class="mb-3">
<label class="form-label">اسم المستخدم أو البريد الإلكتروني</label>
<input type="text" name="login_input" class="form-control" placeholder="ادخل اسم المستخدم أو الإيميل" required>
</div>
<div class="mb-3">
<label class="form-label">كلمة المرور</label>
<div class="input-group">
<input type="password" name="password" id="password_field" class="form-control" placeholder="********" required>
<button type="button" class="btn btn-outline-secondary" onclick="togglePassword(this)">إظهار</button>
</div>
</div>
<button type="submit" class="btn btn-primary w-100">تسجيل الدخول</button>
</form>

<div class="mt-3 text-center">
<p>ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a></p>
</div>
</div>
</div>
</div>
</div>

<script>
function togglePassword(button)
 {
    var passField = document.getElementById('password_field');
    
    if (passField.type === 'password') {
        passField.type = 'text';
        button.textContent = 'إخفاء';
    } 
    else 
        {
        passField.type = 'password';
        button.textContent = 'إظهار';
        }
 }
</script>

<?php include __DIR__ . '/footer.php'; ?>
