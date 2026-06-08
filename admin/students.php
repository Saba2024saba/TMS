<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('admin');

$active = 'students';
$pageTitle = 'Students - TMS';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'create') {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, "student", ?)');
            $stmt->execute([trim($_POST['name']), trim($_POST['email']), password_hash($_POST['password'], PASSWORD_DEFAULT), trim($_POST['phone'])]);
            $userId = (int) $pdo->lastInsertId();
            $stmt = $pdo->prepare('INSERT INTO students (user_id, class_name) VALUES (?, ?)');
            $stmt->execute([$userId, trim($_POST['class_name'])]);
            $pdo->commit();
        } elseif ($action === 'update') {
            $stmt = $pdo->prepare('UPDATE users u JOIN students s ON s.user_id = u.id SET u.name = ?, u.email = ?, u.phone = ?, s.class_name = ? WHERE s.id = ?');
            $stmt->execute([trim($_POST['name']), trim($_POST['email']), trim($_POST['phone']), trim($_POST['class_name']), (int) $_POST['id']]);
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare('DELETE u FROM users u JOIN students s ON s.user_id = u.id WHERE s.id = ?');
            $stmt->execute([(int) $_POST['id']]);
        }
        redirect_to('/admin/students.php');
        exit;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}

$students = db_fetch_all($pdo, '
    SELECT s.id, s.class_name, u.name, u.email, u.phone
    FROM students s
    JOIN users u ON s.user_id = u.id
    ORDER BY u.name
');

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Students</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudent">Add Student</button>
        </div>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Name</th><th>Email</th><th>Phone</th><th>Class</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= e($student['name']) ?></td>
                            <td><?= e($student['email']) ?></td>
                            <td><?= e($student['phone']) ?></td>
                            <td><?= e($student['class_name']) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editStudent<?= (int) $student['id'] ?>">Edit</button>
                                <form method="post" class="d-inline" onsubmit="return confirm('Delete this student and related schedules?');">
                                    <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $student['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <div class="modal fade" id="editStudent<?= (int) $student['id'] ?>" tabindex="-1">
                            <div class="modal-dialog"><div class="modal-content"><form method="post">
                                <div class="modal-header"><h5 class="modal-title">Edit Student</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= (int) $student['id'] ?>">
                                    <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="<?= e($student['name']) ?>" required></div>
                                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($student['email']) ?>" required></div>
                                    <div class="mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= e($student['phone']) ?>"></div>
                                    <div class="mb-3"><label class="form-label">Class</label><input name="class_name" class="form-control" value="<?= e($student['class_name']) ?>" required></div>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
                            </form></div></div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$students): ?><tr><td colspan="5" class="text-center text-muted py-4">No students found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="addStudent" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content"><form method="post">
        <div class="modal-header"><h5 class="modal-title">Add Student</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="create">
            <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Class</label><input name="class_name" class="form-control" required></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
    </form></div></div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

