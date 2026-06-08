<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('admin');

$active = 'subjects';
$pageTitle = 'Subjects - TMS';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $name = trim($_POST['subject_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($action === 'create') {
        $stmt = $pdo->prepare('INSERT INTO subjects (subject_name, description) VALUES (?, ?)');
        $stmt->execute([$name, $description]);
    } elseif ($action === 'update') {
        $stmt = $pdo->prepare('UPDATE subjects SET subject_name = ?, description = ? WHERE id = ?');
        $stmt->execute([$name, $description, (int) $_POST['id']]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM subjects WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
    }

    redirect_to('/admin/subjects.php');
    exit;
}

$subjects = db_fetch_all($pdo, 'SELECT * FROM subjects ORDER BY subject_name');
require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Subjects</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubject">Add Subject</button>
        </div>

        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Name</th><th>Description</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($subjects as $subject): ?>
                        <tr>
                            <td><?= e($subject['subject_name']) ?></td>
                            <td><?= e($subject['description']) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSubject<?= (int) $subject['id'] ?>">Edit</button>
                                <form method="post" class="d-inline" onsubmit="return confirm('Delete this subject?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $subject['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <div class="modal fade" id="editSubject<?= (int) $subject['id'] ?>" tabindex="-1">
                            <div class="modal-dialog"><div class="modal-content">
                                <form method="post">
                                    <div class="modal-header"><h5 class="modal-title">Edit Subject</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="id" value="<?= (int) $subject['id'] ?>">
                                        <div class="mb-3"><label class="form-label">Subject Name</label><input name="subject_name" class="form-control" value="<?= e($subject['subject_name']) ?>" required></div>
                                        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?= e($subject['description']) ?></textarea></div>
                                    </div>
                                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
                                </form>
                            </div></div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$subjects): ?><tr><td colspan="3" class="text-center text-muted py-4">No subjects found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="addSubject" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="post">
            <div class="modal-header"><h5 class="modal-title">Add Subject</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="action" value="create">
                <div class="mb-3"><label class="form-label">Subject Name</label><input name="subject_name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
        </form>
    </div></div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

