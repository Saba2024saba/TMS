<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('admin');

$active = 'tuition_posts';
$pageTitle = 'Tuition Posts - TMS';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        if (($_POST['action'] ?? '') === 'update') {
            $stmt = $pdo->prepare('UPDATE tuition_posts SET title=?, class_name=?, subject=?, area=?, salary=?, teaching_days=?, description=? WHERE id=?');
            $stmt->execute([trim($_POST['title']), trim($_POST['class_name']), trim($_POST['subject']), trim($_POST['area']), (float) $_POST['salary'], trim($_POST['teaching_days']), trim($_POST['description']), (int) $_POST['id']]);
            flash('success', 'Tuition post updated.');
        } elseif (($_POST['action'] ?? '') === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM tuition_posts WHERE id=?');
            $stmt->execute([(int) $_POST['id']]);
            flash('success', 'Tuition post deleted.');
        }
        redirect_to('/admin/tuition_posts.php');
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$q = trim((string) ($_GET['q'] ?? ''));
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(p.title LIKE ? OR p.subject LIKE ? OR p.area LIKE ? OR u.name LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%");
}
$sql = 'SELECT p.*, u.name AS posted_by FROM tuition_posts p JOIN users u ON u.id=p.user_id' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY p.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <h1 class="h3 mb-4">Tuition Posts</h1>
        <?php render_flash_messages(); ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form class="card table-card mb-3" method="get"><div class="card-body row g-3"><div class="col-md-9"><input name="q" class="form-control" placeholder="Search title, subject, area, poster" value="<?= e($q) ?>"></div><div class="col-md-3"><button class="btn btn-primary">Search</button> <a class="btn btn-light" href="<?= e(url('/admin/tuition_posts.php')) ?>">Clear</a></div></div></form>
        <div class="card table-card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Title</th><th>Subject</th><th>Area</th><th>Salary</th><th>Posted By</th><th class="text-end">Actions</th></tr></thead><tbody>
            <?php foreach ($posts as $post): ?>
                <tr><td><?= e($post['title']) ?></td><td><?= e($post['subject']) ?></td><td><?= e($post['area']) ?></td><td><?= e(number_format((float) $post['salary'], 2)) ?></td><td><?= e($post['posted_by']) ?></td><td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('/tuition/show.php?id=' . (int) $post['id'])) ?>">View</a>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPost<?= (int) $post['id'] ?>">Edit</button>
                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this tuition post?');"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $post['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                </td></tr>
                <div class="modal fade" id="editPost<?= (int) $post['id'] ?>" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><form method="post">
                    <?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                    <div class="modal-header"><h5 class="modal-title">Edit Tuition Post</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body row g-3">
                        <div class="col-12"><label class="form-label">Title</label><input name="title" class="form-control" value="<?= e($post['title']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Class Name</label><input name="class_name" class="form-control" value="<?= e($post['class_name']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Subject</label><input name="subject" class="form-control" value="<?= e($post['subject']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Area</label><input name="area" class="form-control" value="<?= e($post['area']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Salary</label><input type="number" min="0" step="0.01" name="salary" class="form-control" value="<?= e((string) $post['salary']) ?>" required></div>
                        <div class="col-12"><label class="form-label">Teaching Days</label><input name="teaching_days" class="form-control" value="<?= e($post['teaching_days']) ?>" required></div>
                        <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4" required><?= e($post['description']) ?></textarea></div>
                    </div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
                </form></div></div></div>
            <?php endforeach; ?>
            <?php if (!$posts): ?><tr><td colspan="6" class="text-center text-muted py-4">No tuition posts found.</td></tr><?php endif; ?>
            </tbody></table></div></div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
