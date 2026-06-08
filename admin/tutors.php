<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('admin');

$active = 'tutors';
$pageTitle = 'Tutors - TMS';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, "tutor", ?)');
            $stmt->execute([
                trim($_POST['name']),
                trim($_POST['email']),
                password_hash($_POST['password'], PASSWORD_DEFAULT),
                trim($_POST['phone']),
            ]);
            $userId = (int) $pdo->lastInsertId();
            $stmt = $pdo->prepare('INSERT INTO tutors (user_id, subject_id, experience) VALUES (?, ?, ?)');
            $stmt->execute([$userId, (int) $_POST['subject_id'], (int) $_POST['experience']]);
            $pdo->commit();
        } elseif ($action === 'update') {
            $stmt = $pdo->prepare('UPDATE users u JOIN tutors t ON t.user_id = u.id SET u.name = ?, u.email = ?, u.phone = ?, t.subject_id = ?, t.experience = ? WHERE t.id = ?');
            $stmt->execute([
                trim($_POST['name']),
                trim($_POST['email']),
                trim($_POST['phone']),
                (int) $_POST['subject_id'],
                (int) $_POST['experience'],
                (int) $_POST['id'],
            ]);
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare('DELETE u FROM users u JOIN tutors t ON t.user_id = u.id WHERE t.id = ?');
            $stmt->execute([(int) $_POST['id']]);
        }
        redirect_to('/admin/tutors.php');
        exit;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}

$subjects = db_fetch_all($pdo, 'SELECT id, subject_name FROM subjects ORDER BY subject_name');
$tutors = db_fetch_all($pdo, '
    SELECT t.id, t.experience, t.subject_id, u.name, u.email, u.phone, s.subject_name
    FROM tutors t
    JOIN users u ON t.user_id = u.id
    JOIN subjects s ON t.subject_id = s.id
    ORDER BY u.name
');

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Tutors</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTutor">Add Tutor</button>
        </div>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Name</th><th>Email</th><th>Phone</th><th>Subject</th><th>Experience</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($tutors as $tutor): ?>
                        <tr>
                            <td><?= e($tutor['name']) ?></td>
                            <td><?= e($tutor['email']) ?></td>
                            <td><?= e($tutor['phone']) ?></td>
                            <td><?= e($tutor['subject_name']) ?></td>
                            <td><?= (int) $tutor['experience'] ?> years</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTutor<?= (int) $tutor['id'] ?>">Edit</button>
                                <form method="post" class="d-inline" onsubmit="return confirm('Delete this tutor and related schedules?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $tutor['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <div class="modal fade" id="editTutor<?= (int) $tutor['id'] ?>" tabindex="-1">
                            <div class="modal-dialog"><div class="modal-content">
                                <form method="post">
                                    <div class="modal-header"><h5 class="modal-title">Edit Tutor</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="id" value="<?= (int) $tutor['id'] ?>">
                                        <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="<?= e($tutor['name']) ?>" required></div>
                                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($tutor['email']) ?>" required></div>
                                        <div class="mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= e($tutor['phone']) ?>"></div>
                                        <div class="mb-3"><label class="form-label">Subject</label><select name="subject_id" class="form-select" required>
                                            <?php foreach ($subjects as $subject): ?><option value="<?= (int) $subject['id'] ?>" <?= (int) $subject['id'] === (int) $tutor['subject_id'] ? 'selected' : '' ?>><?= e($subject['subject_name']) ?></option><?php endforeach; ?>
                                        </select></div>
                                        <div class="mb-3"><label class="form-label">Experience</label><input type="number" min="0" name="experience" class="form-control" value="<?= (int) $tutor['experience'] ?>" required></div>
                                    </div>
                                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
                                </form>
                            </div></div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$tutors): ?><tr><td colspan="6" class="text-center text-muted py-4">No tutors found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="addTutor" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="post">
            <div class="modal-header"><h5 class="modal-title">Add Tutor</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="action" value="create">
                <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Subject</label><select name="subject_id" class="form-select" required>
                    <?php foreach ($subjects as $subject): ?><option value="<?= (int) $subject['id'] ?>"><?= e($subject['subject_name']) ?></option><?php endforeach; ?>
                </select></div>
                <div class="mb-3"><label class="form-label">Experience</label><input type="number" min="0" name="experience" class="form-control" value="0" required></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
        </form>
    </div></div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

