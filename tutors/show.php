<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM tutor_profiles WHERE id = ?');
$stmt->execute([$id]);
$profile = $stmt->fetch();
if (!$profile) {
    http_response_code(404);
    die('Tutor profile not found.');
}

$pageTitle = $profile['full_name'] . ' - TMS';
require_once __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="card table-card">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap gap-4 align-items-center mb-4">
                <?php if ($profile['profile_picture']): ?>
                    <img class="profile-photo" src="<?= e(url('/' . $profile['profile_picture'])) ?>" alt="">
                <?php else: ?>
                    <div class="profile-photo profile-placeholder"><?= e(strtoupper(substr($profile['full_name'], 0, 1))) ?></div>
                <?php endif; ?>
                <div>
                    <h1 class="h3 mb-1"><?= e($profile['full_name']) ?></h1>
                    <div class="text-muted"><?= e($profile['department']) ?>, <?= e($profile['university']) ?></div>
                </div>
            </div>
            <dl class="row">
                <dt class="col-md-3">Phone</dt><dd class="col-md-9"><?= e($profile['phone']) ?></dd>
                <dt class="col-md-3">Gender</dt><dd class="col-md-9"><?= e($profile['gender']) ?></dd>
                <dt class="col-md-3">Academic Year</dt><dd class="col-md-9"><?= e($profile['academic_year']) ?></dd>
                <dt class="col-md-3">CGPA</dt><dd class="col-md-9"><?= e((string) ($profile['cgpa'] ?? '')) ?></dd>
                <dt class="col-md-3">Preferred Subjects</dt><dd class="col-md-9"><?= e($profile['preferred_subjects']) ?></dd>
                <dt class="col-md-3">Preferred Areas</dt><dd class="col-md-9"><?= e($profile['preferred_areas']) ?></dd>
                <dt class="col-md-3">Teaching Experience</dt><dd class="col-md-9"><?= nl2br(e($profile['teaching_experience'])) ?></dd>
                <dt class="col-md-3">Bio</dt><dd class="col-md-9"><?= nl2br(e($profile['bio'])) ?></dd>
            </dl>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
