<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

if (current_user()['role'] === 'student') {
    flash('danger', 'Access denied. Students cannot post tuition opportunities.');
    redirect_to('/tuition/index.php');
}

$pageTitle = 'Post Tuition - TMS';
$errors = [];
$values = [
    'title' => '',
    'class_name' => '',
    'subject' => '',
    'area' => '',
    'salary' => '',
    'teaching_days' => '',
    'description' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($values as $field => $_) {
        $values[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    foreach (['title', 'class_name', 'subject', 'area', 'salary', 'teaching_days', 'description'] as $field) {
        if ($values[$field] === '') {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }

    if ($values['salary'] !== '' && (!is_numeric($values['salary']) || (float) $values['salary'] < 0)) {
        $errors[] = 'Salary must be a valid positive amount.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('
            INSERT INTO tuition_posts (user_id, title, class_name, subject, area, salary, teaching_days, description)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            current_user()['id'],
            $values['title'],
            $values['class_name'],
            $values['subject'],
            $values['area'],
            (float) $values['salary'],
            $values['teaching_days'],
            $values['description'],
        ]);
        flash('success', 'Tuition post created successfully.');
        redirect_to('/tuition/show.php?id=' . (int) $pdo->lastInsertId());
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card table-card">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3">Post Tuition</h1>
                    <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
                    <form method="post">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-12"><label class="form-label">Title</label><input name="title" class="form-control" value="<?= e($values['title']) ?>" required></div>
                            <div class="col-md-6"><label class="form-label">Class Name</label><input name="class_name" class="form-control" value="<?= e($values['class_name']) ?>" required></div>
                            <div class="col-md-6"><label class="form-label">Subject</label><input name="subject" class="form-control" value="<?= e($values['subject']) ?>" required></div>
                            <div class="col-md-6"><label class="form-label">Area</label><input name="area" class="form-control" value="<?= e($values['area']) ?>" required></div>
                            <div class="col-md-6"><label class="form-label">Salary</label><input type="number" min="0" step="0.01" name="salary" class="form-control" value="<?= e($values['salary']) ?>" required></div>
                            <div class="col-md-12"><label class="form-label">Teaching Days</label><input name="teaching_days" class="form-control" value="<?= e($values['teaching_days']) ?>" required></div>
                            <div class="col-md-12"><label class="form-label">Description</label><textarea name="description" rows="5" class="form-control" required><?= e($values['description']) ?></textarea></div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button class="btn btn-primary">Publish</button>
                            <a class="btn btn-light" href="<?= e(url('/tuition/index.php')) ?>">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
