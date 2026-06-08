<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Tutor Profiles - TMS';
$subject = trim((string) ($_GET['subject'] ?? ''));
$area = trim((string) ($_GET['area'] ?? ''));
$university = trim((string) ($_GET['university'] ?? ''));
$where = [];
$params = [];

if ($subject !== '') {
    $where[] = 'preferred_subjects LIKE ?';
    $params[] = '%' . $subject . '%';
}
if ($area !== '') {
    $where[] = 'preferred_areas LIKE ?';
    $params[] = '%' . $area . '%';
}
if ($university !== '') {
    $where[] = 'university LIKE ?';
    $params[] = '%' . $university . '%';
}

$sql = 'SELECT * FROM tutor_profiles';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY created_at DESC, id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$profiles = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <h1 class="h3 mb-3">Tutor Profiles</h1>
    <form class="card table-card mb-4" method="get">
        <div class="card-body row g-3">
            <div class="col-md-4"><label class="form-label">Subject</label><input name="subject" class="form-control" value="<?= e($subject) ?>"></div>
            <div class="col-md-4"><label class="form-label">Area</label><input name="area" class="form-control" value="<?= e($area) ?>"></div>
            <div class="col-md-4"><label class="form-label">University</label><input name="university" class="form-control" value="<?= e($university) ?>"></div>
            <div class="col-12"><button class="btn btn-primary">Search</button> <a class="btn btn-light" href="<?= e(url('/tutors/index.php')) ?>">Clear</a></div>
        </div>
    </form>
    <div class="row g-3">
        <?php foreach ($profiles as $profile): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card table-card h-100">
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <?php if ($profile['profile_picture']): ?>
                                <img class="profile-thumb" src="<?= e(url('/' . $profile['profile_picture'])) ?>" alt="">
                            <?php else: ?>
                                <div class="profile-thumb profile-placeholder"><?= e(strtoupper(substr($profile['full_name'], 0, 1))) ?></div>
                            <?php endif; ?>
                            <div>
                                <h2 class="h5 mb-1"><?= e($profile['full_name']) ?></h2>
                                <div class="text-muted small"><?= e($profile['university']) ?></div>
                            </div>
                        </div>
                        <dl class="row small mt-3 mb-3">
                            <dt class="col-5">Subjects</dt><dd class="col-7"><?= e($profile['preferred_subjects']) ?></dd>
                            <dt class="col-5">Areas</dt><dd class="col-7"><?= e($profile['preferred_areas']) ?></dd>
                        </dl>
                        <a class="btn btn-outline-primary w-100" href="<?= e(url('/tutors/show.php?id=' . (int) $profile['id'])) ?>">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (!$profiles): ?><div class="col-12"><div class="alert alert-info">No tutor profiles found.</div></div><?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
