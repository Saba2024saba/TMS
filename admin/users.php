<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('admin');

$active = 'users';
$pageTitle = 'Users - TMS';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'update') {
            $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, phone = ?, role = ? WHERE id = ?');
            $stmt->execute([trim($_POST['name']), trim($_POST['email']), trim($_POST['phone']), $_POST['role'], (int) $_POST['id']]);
            flash('success', 'User updated.');
        } elseif ($action === 'delete') {
            if ((int) $_POST['id'] === (int) current_user()['id']) {
                flash('danger', 'You cannot delete your own account.');
            } else {
                $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
                $stmt->execute([(int) $_POST['id']]);
                flash('success', 'User deleted.');
            }
        }
        redirect_to('/admin/users.php');
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$q = trim((string) ($_GET['q'] ?? ''));
$role = trim((string) ($_GET['role'] ?? ''));
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%");
}
if (in_array($role, ['admin', 'tutor', 'student'], true)) {
    $where[] = 'role = ?';
    $params[] = $role;
}
$sql = 'SELECT * FROM users' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC, id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <h1 class="h3 mb-4">Users</h1>
        <?php render_flash_messages(); ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form class="card table-card mb-3" method="get"><div class="card-body row g-3">
            <div class="col-md-6"><input name="q" class="form-control" placeholder="Search name, email, phone" value="<?= e($q) ?>"></div>
            <div class="col-md-3"><select name="role" class="form-select"><option value="">All roles</option><?php foreach (['admin','tutor','student'] as $item): ?><option value="<?= e($item) ?>" <?= $role === $item ? 'selected' : '' ?>><?= e(ucfirst($item)) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><button class="btn btn-primary">Filter</button> <a class="btn btn-light" href="<?= e(url('/admin/users.php')) ?>">Clear</a></div>
        </div></form>
        <div class="card table-card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th class="text-end">Actions</th></tr></thead><tbody>
            <?php foreach ($users as $user): ?>
                <tr><td><?= e($user['name']) ?></td><td><?= e($user['email']) ?></td><td><?= e($user['phone']) ?></td><td><?= e($user['role']) ?></td><td class="text-end">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUser<?= (int) $user['id'] ?>">Edit</button>
                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this user?');"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $user['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                </td></tr>
                <div class="modal fade" id="editUser<?= (int) $user['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="post">
                    <?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                    <div class="modal-header"><h5 class="modal-title">Edit User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="<?= e($user['name']) ?>" required></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required></div>
                        <div class="mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= e($user['phone']) ?>"></div>
                        <div class="mb-3"><label class="form-label">Role</label><select name="role" class="form-select" required><?php foreach (['admin','tutor','student'] as $item): ?><option value="<?= e($item) ?>" <?= $user['role'] === $item ? 'selected' : '' ?>><?= e(ucfirst($item)) ?></option><?php endforeach; ?></select></div>
                    </div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
                </form></div></div></div>
            <?php endforeach; ?>
            <?php if (!$users): ?><tr><td colspan="5" class="text-center text-muted py-4">No users found.</td></tr><?php endif; ?>
            </tbody></table></div></div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
