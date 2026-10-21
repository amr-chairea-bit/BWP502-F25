    <?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
    }

    require_once 'db.php';

    $search_input = "";
    $limit = 10; 
    $page = 1;

    if (isset($_GET['search'])) {
    $search_input = trim($_GET['search']);
    }

    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $page = (int)$_GET['page'];
    if ($page < 1) $page = 1;
    }

    $offset = ($page - 1) * $limit;
    $where_clause = "";
    $params = [];

    if (!empty($search_input)) {
    $where_clause = "WHERE name LIKE :search OR email LIKE :search OR phone LIKE :search";
    $params[':search'] = "%" . $search_input . "%";
    }

    $count_query = "SELECT COUNT(*) AS total FROM students $where_clause";
    $count_statement = $connection->prepare($count_query);
    $count_statement->execute($params);
    $total_students = $count_statement->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_students / $limit);

    $students_query = "SELECT * FROM students $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $students_statement = $connection->prepare($students_query);
    $students_statement->execute($params);
    $students = $students_statement->fetchAll(PDO::FETCH_ASSOC);

    include __DIR__ . '/header.php';
    ?>

    <div class="row justify-content-center">
    <div class="col-md-10">
    <div class="card shadow">
        <div class="card-header bg-info text-white text-center">
            <h3>قائمة الطلاب</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="ابحث بالاسم أو الإيميل أو الهاتف..." value="<?php echo htmlspecialchars($search_input); ?>">
                    <button type="submit" class="btn btn-primary">بحث</button>
                    <a href="view_students.php" class="btn btn-secondary">إلغاء البحث</a>
                </div>
            </form>
            <div class="mb-3">
                <a href="add_student.php" class="btn btn-success">+ إضافة طالب جديد</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>الإيميل</th>
                            <th>الهاتف</th>
                            <th>العنوان</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($students) > 0): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['id']; ?></td>
                                    <td>
                                        <?php if (!empty($student['profile_picture']) && file_exists($student['profile_picture'])): ?>
                                            <img src="<?php echo $student['profile_picture']; ?>" width="50" height="50" style="object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <span class="text-muted">لا صورة</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($student['address']); ?></td>
                                    <td>
                                        <a href="update_student.php?id=<?php echo $student['id']; ?>" class="btn btn-warning btn-sm">تعديل</a>
                                        <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الطالب؟');">حذف</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">لا يوجد طلاب لعرضهم.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if ($total_pages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search_input); ?>">السابق</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_input); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search_input); ?>">التالي</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
    </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

