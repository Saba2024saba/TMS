<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Tuition Posts - TMS';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;
$q = trim((string) ($_GET['q'] ?? ''));
$subject = trim((string) ($_GET['subject'] ?? ''));
$area = trim((string) ($_GET['area'] ?? ''));
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(title LIKE :q OR description LIKE :q)';
    $params[':q'] = "%$q%";
}
if ($subject !== '') {
    $where[] = 'subject LIKE :subject';
    $params[':subject'] = "%$subject%";
}
if ($area !== '') {
    $where[] = 'area LIKE :area';
    $params[':area'] = "%$area%";
}
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare('SELECT COUNT(*) FROM tuition_posts' . $whereSql);
foreach ($params as $name => $value) {
    $countStmt->bindValue($name, $value);
}
$countStmt->execute();
$total = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));

$stmt = $pdo->prepare('
    SELECT id, title, subject, area, salary, created_at
    FROM tuition_posts
    ' . $whereSql . '
    ORDER BY created_at DESC, id DESC
    LIMIT :limit OFFSET :offset
');
foreach ($params as $name => $value) {
    $stmt->bindValue($name, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();
$queryString = http_build_query(array_filter(['q' => $q, 'subject' => $subject, 'area' => $area], fn($value) => $value !== ''));

require_once __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Tuition Posts</h1>
            <div class="text-muted">Latest available tuition advertisements.</div>
        </div>
        <?php if (current_user() && current_user()['role'] !== 'student'): ?>
            <a href="<?= e(url('/tuition/create.php')) ?>" class="btn btn-primary">Post Tuition</a>
        <?php endif; ?>
    </div>

    <?php render_flash_messages(); ?>

    <form class="card table-card mb-4" method="get">
        <div class="card-body row g-3">
            <div class="col-md-4"><label class="form-label">Search</label><input name="q" class="form-control" value="<?= e($q) ?>"></div>
            <div class="col-md-3"><label class="form-label">Subject</label><input name="subject" class="form-control" value="<?= e($subject) ?>"></div>
            <div class="col-md-3"><label class="form-label">Area</label><input name="area" class="form-control" value="<?= e($area) ?>"></div>
            <div class="col-md-2 d-flex align-items-end gap-2"><button class="btn btn-primary">Filter</button><a class="btn btn-light" href="<?= e(url('/tuition/index.php')) ?>">Clear</a></div>
        </div>
    </form>

    <div class="row g-3">
        <?php foreach ($posts as $post): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card table-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h2 class="h5"><?= e($post['title']) ?></h2>
                        <div class="text-muted small mb-3"><?= e(date('M d, Y', strtotime($post['created_at']))) ?></div>
                        <dl class="row small mb-3">
                            <dt class="col-4">Subject</dt><dd class="col-8"><?= e($post['subject']) ?></dd>
                            <dt class="col-4">Area</dt><dd class="col-8"><?= e($post['area']) ?></dd>
                            <dt class="col-4">Salary</dt><dd class="col-8"><?= e(number_format((float) $post['salary'], 2)) ?></dd>
                        </dl>
                        <a class="btn <?= current_user() && current_user()['role'] === 'student' ? 'btn-primary' : 'btn-outline-primary' ?> mt-auto" href="<?= e(url('/tuition/show.php?id=' . (int) $post['id'])) ?>">
                            <?= current_user() && current_user()['role'] === 'student' ? 'Apply' : 'View Details' ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (!$posts): ?>
            <div class="col-12"><div class="alert alert-info">No tuition posts are available yet.</div></div>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= e(url('/tuition/index.php?' . ($queryString ? $queryString . '&' : '') . 'page=' . $i)) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
