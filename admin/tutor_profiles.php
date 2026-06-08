<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('admin');

$active = 'tutor_profiles';
$pageTitle = 'Tutor Profiles - TMS';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        if (($_POST['action'] ?? '') === 'update') {
            $stmt = $pdo->prepare('UPDATE tutor_profiles SET full_name=?, phone=?, university=?, department=?, academic_year=?, cgpa=?, teaching_experience=?, preferred_subjects=?, preferred_areas=?, bio=? WHERE id=?');
            $stmt->execute([
                trim($_POST['full_name']), trim($_POST['phone']), trim($_POST['university']), trim($_POST['department']),
                trim($_POST['academic_year']), $_POST['cgpa'] === '' ? null : (float) $_POST['cgpa'], trim($_POST['teaching_experience']),
                trim($_POST['preferred_subjects']), trim($_POST['preferred_areas']), trim($_POST['bio']), (int) $_POST['id'],
            ]);
            flash('success', 'Tutor profile updated.');
        } elseif (($_POST['action'] ?? '') === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM tutor_profiles WHERE id=?');
            $stmt->execute([(int) $_POST['id']]);
            flash('success', 'Tutor profile deleted.');
        }
        redirect_to('/admin/tutor_profiles.php');
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$q = trim((string) ($_GET['q'] ?? ''));
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(full_name LIKE ? OR preferred_subjects LIKE ? OR preferred_areas LIKE ? OR university LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%");
}
$sql = 'SELECT * FROM tutor_profiles' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$profiles = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <h1 class="h3 mb-4">Tutor Profiles</h1>
        <?php render_flash_messages(); ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form class="card table-card mb-3" method="get"><div class="card-body row g-3"><div class="col-md-9"><input name="q" class="form-control" placeholder="Search subject, area, university, name" value="<?= e($q) ?>"></div><div class="col-md-3"><button class="btn btn-primary">Search</button> <a class="btn btn-light" href="<?= e(url('/admin/tutor_profiles.php')) ?>">Clear</a></div></div></form>
        <div class="card table-card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Name</th><th>University</th><th>Subjects</th><th>Areas</th><th class="text-end">Actions</th></tr></thead><tbody>
            <?php foreach ($profiles as $profile): ?>
                <tr><td><?= e($profile['full_name']) ?></td><td><?= e($profile['university']) ?></td><td><?= e($profile['preferred_subjects']) ?></td><td><?= e($profile['preferred_areas']) ?></td><td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('/tutors/show.php?id=' . (int) $profile['id'])) ?>">View</a>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfile<?= (int) $profile['id'] ?>">Edit</button>
                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this tutor profile?');"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $profile['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                </td></tr>
                <div class="modal fade" id="editProfile<?= (int) $profile['id'] ?>" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><form method="post">
                    <?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= (int) $profile['id'] ?>">
                    <div class="modal-header"><h5 class="modal-title">Edit Tutor Profile</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body row g-3">
                        <div class="col-md-6"><label class="form-label">Full Name</label><input name="full_name" class="form-control" value="<?= e($profile['full_name']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= e($profile['phone']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">University</label><input name="university" class="form-control" value="<?= e($profile['university']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Department</label><input name="department" class="form-control" value="<?= e($profile['department']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Academic Year</label><input name="academic_year" class="form-control" value="<?= e($profile['academic_year']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">CGPA</label><input type="number" step="0.01" min="0" max="4" name="cgpa" class="form-control" value="<?= e((string) $profile['cgpa']) ?>"></div>
                        <div class="col-md-6"><label class="form-label">Preferred Subjects</label><input name="preferred_subjects" class="form-control" value="<?= e($profile['preferred_subjects']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Preferred Areas</label><input name="preferred_areas" class="form-control" value="<?= e($profile['preferred_areas']) ?>" required></div>
                        <div class="col-12"><label class="form-label">Teaching Experience</label><textarea name="teaching_experience" class="form-control" rows="3" required><?= e($profile['teaching_experience']) ?></textarea></div>
                        <div class="col-12"><label class="form-label">Bio</label><textarea name="bio" class="form-control" rows="3"><?= e($profile['bio']) ?></textarea></div>
                    </div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
                </form></div></div></div>
            <?php endforeach; ?>
            <?php if (!$profiles): ?><tr><td colspan="5" class="text-center text-muted py-4">No tutor profiles found.</td></tr><?php endif; ?>
            </tbody></table></div></div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
