<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (current_user()) {
    redirect_by_role(current_user()['role']);
}

$pageTitle = 'Registration - TMS';
$errors = [];
$subjects = db_fetch_all($pdo, 'SELECT id, subject_name FROM subjects ORDER BY subject_name');
$values = [
    'account_type' => $_POST['account_type'] ?? 'student',
    'name' => '',
    'email' => '',
    'phone' => '',
    'class_name' => '',
    'subject_id' => $subjects[0]['id'] ?? '',
    'experience' => '0',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach (['name', 'email', 'phone', 'class_name', 'subject_id', 'experience'] as $field) {
        $values[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    $values['account_type'] = $_POST['account_type'] ?? 'student';
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if (!in_array($values['account_type'], ['student', 'teacher'], true)) {
        $errors[] = 'Please choose a valid account type.';
    }
    if ($values['name'] === '') {
        $errors[] = 'Name is required.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Password confirmation does not match.';
    }
    if ($values['account_type'] === 'student' && $values['class_name'] === '') {
        $errors[] = 'Class name is required for student accounts.';
    }
    if ($values['account_type'] === 'teacher') {
        if ($values['subject_id'] === '') {
            $errors[] = 'Subject is required for teacher accounts.';
        }
        if ($values['experience'] === '' || !ctype_digit($values['experience'])) {
            $errors[] = 'Experience must be a valid number.';
        }
    }

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $role = $values['account_type'] === 'teacher' ? 'tutor' : 'student';
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([
                $values['name'],
                $values['email'],
                password_hash($password, PASSWORD_DEFAULT),
                $role,
                $values['phone'],
            ]);
            $userId = (int) $pdo->lastInsertId();

            if ($role === 'student') {
                $stmt = $pdo->prepare('INSERT INTO students (user_id, class_name) VALUES (?, ?)');
                $stmt->execute([$userId, $values['class_name']]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO tutors (user_id, subject_id, experience) VALUES (?, ?, ?)');
                $stmt->execute([$userId, (int) $values['subject_id'], (int) $values['experience']]);
            }

            $pdo->commit();
            flash('success', 'Registration successful. Please log in.');
            redirect_to('/login');
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = str_contains($e->getMessage(), 'Duplicate') ? 'This email address is already registered.' : 'Registration failed. Please try again.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card table-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="mb-4">
                        <h1 class="h3 mb-1">Create Your Account</h1>
                        <p class="text-muted mb-0">Register as a student or teacher to start using TMS.</p>
                    </div>
                    <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
                    <form method="post" id="registrationForm">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Account Type</label>
                                <select name="account_type" id="accountType" class="form-select" required>
                                    <option value="student" <?= $values['account_type'] === 'student' ? 'selected' : '' ?>>Student</option>
                                    <option value="teacher" <?= $values['account_type'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input name="phone" class="form-control" value="<?= e($values['phone']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input name="name" class="form-control" value="<?= e($values['name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= e($values['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" minlength="6" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                            </div>
                            <div class="col-md-12 account-fields student-fields">
                                <label class="form-label">Class Name</label>
                                <input name="class_name" class="form-control" value="<?= e($values['class_name']) ?>">
                            </div>
                            <div class="col-md-6 account-fields teacher-fields">
                                <label class="form-label">Subject</label>
                                <select name="subject_id" class="form-select">
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= (int) $subject['id'] ?>" <?= (int) $values['subject_id'] === (int) $subject['id'] ? 'selected' : '' ?>><?= e($subject['subject_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 account-fields teacher-fields">
                                <label class="form-label">Experience</label>
                                <input type="number" min="0" name="experience" class="form-control" value="<?= e($values['experience']) ?>">
                            </div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
                            <button class="btn btn-primary">Register</button>
                            <a class="btn btn-light" href="<?= e(url('/login')) ?>">Already have an account?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    const accountType = document.getElementById('accountType');
    const updateAccountFields = () => {
        const isTeacher = accountType.value === 'teacher';
        document.querySelectorAll('.student-fields').forEach((field) => field.classList.toggle('d-none', isTeacher));
        document.querySelectorAll('.teacher-fields').forEach((field) => field.classList.toggle('d-none', !isTeacher));
    };
    accountType.addEventListener('change', updateAccountFields);
    updateAccountFields();
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
