<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('
    SELECT p.*, u.name AS posted_by
    FROM tuition_posts p
    JOIN users u ON u.id = p.user_id
    WHERE p.id = ?
');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    die('Tuition post not found.');
}

$student = null;
$alreadyApplied = false;
if (current_user() && current_user()['role'] === 'student') {
    $stmt = $pdo->prepare('SELECT id FROM students WHERE user_id = ?');
    $stmt->execute([current_user()['id']]);
    $student = $stmt->fetch();
    if ($student) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM tuition_applications WHERE student_id = ? AND tuition_id = ?');
        $stmt->execute([(int) $student['id'], $id]);
        $alreadyApplied = (int) $stmt->fetchColumn() > 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login('student');
    verify_csrf();

    if (!$student) {
        flash('danger', 'Student profile not found.');
        redirect_to('/student/dashboard.php');
    }

    $message = trim((string) ($_POST['application_message'] ?? ''));
    if ($message === '') {
        flash('danger', 'Application message is required.');
    } elseif ($alreadyApplied) {
        flash('warning', 'You have already applied for this tuition.');
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO tuition_applications (student_id, tuition_id, application_message) VALUES (?, ?, ?)');
            $stmt->execute([(int) $student['id'], $id, $message]);
            flash('success', 'Application submitted successfully.');
        } catch (Throwable $e) {
            flash('danger', 'Unable to submit this application.');
        }
    }
    redirect_to('/tuition/show.php?id=' . $id);
}

$pageTitle = $post['title'] . ' - TMS';
require_once __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <?php render_flash_messages(); ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card table-card">
                <div class="card-body p-4">
                    <h1 class="h3"><?= e($post['title']) ?></h1>
                    <div class="text-muted mb-4">Posted by <?= e($post['posted_by']) ?> on <?= e(date('M d, Y', strtotime($post['created_at']))) ?></div>
                    <dl class="row">
                        <dt class="col-sm-4">Class Name</dt><dd class="col-sm-8"><?= e($post['class_name']) ?></dd>
                        <dt class="col-sm-4">Subject</dt><dd class="col-sm-8"><?= e($post['subject']) ?></dd>
                        <dt class="col-sm-4">Area</dt><dd class="col-sm-8"><?= e($post['area']) ?></dd>
                        <dt class="col-sm-4">Salary</dt><dd class="col-sm-8"><?= e(number_format((float) $post['salary'], 2)) ?></dd>
                        <dt class="col-sm-4">Teaching Days</dt><dd class="col-sm-8"><?= e($post['teaching_days']) ?></dd>
                    </dl>
                    <h2 class="h5 mt-4">Description</h2>
                    <p class="mb-0"><?= nl2br(e($post['description'])) ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <?php if (current_user() && current_user()['role'] === 'student'): ?>
                <div class="card table-card">
                    <div class="card-body">
                        <h2 class="h5">Apply Now</h2>
                        <?php if (!$student): ?>
                            <div class="alert alert-warning mb-0">Student profile not found for this account.</div>
                        <?php elseif ($alreadyApplied): ?>
                            <div class="alert alert-info mb-0">You already applied for this tuition.</div>
                        <?php else: ?>
                            <form method="post">
                                <?= csrf_field() ?>
                                <div class="mb-3"><label class="form-label">Application Message</label><textarea name="application_message" rows="5" class="form-control" required></textarea></div>
                                <button class="btn btn-primary w-100">Apply Now</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
