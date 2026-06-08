<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('tutor');

$active = 'profile';
$pageTitle = 'Tutor Profile - TMS';
$errors = [];

$stmt = $pdo->prepare('SELECT * FROM tutor_profiles WHERE user_id = ?');
$stmt->execute([current_user()['id']]);
$profile = $stmt->fetch();

$values = [
    'full_name' => $profile['full_name'] ?? current_user()['name'],
    'phone' => $profile['phone'] ?? '',
    'gender' => $profile['gender'] ?? 'Male',
    'university' => $profile['university'] ?? '',
    'department' => $profile['department'] ?? '',
    'academic_year' => $profile['academic_year'] ?? '',
    'cgpa' => $profile['cgpa'] ?? '',
    'teaching_experience' => $profile['teaching_experience'] ?? '',
    'preferred_subjects' => $profile['preferred_subjects'] ?? '',
    'preferred_areas' => $profile['preferred_areas'] ?? '',
    'bio' => $profile['bio'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($values as $field => $_) {
        $values[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    foreach (['full_name', 'phone', 'gender', 'university', 'department', 'academic_year', 'teaching_experience', 'preferred_subjects', 'preferred_areas'] as $field) {
        if ($values[$field] === '') {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }

    if (!in_array($values['gender'], ['Male', 'Female', 'Other'], true)) {
        $errors[] = 'Gender is invalid.';
    }

    if ($values['cgpa'] !== '' && (!is_numeric($values['cgpa']) || (float) $values['cgpa'] < 0 || (float) $values['cgpa'] > 4)) {
        $errors[] = 'CGPA must be between 0 and 4.';
    }

    $picturePath = $profile['profile_picture'] ?? null;
    if (!empty($_FILES['profile_picture']['name'])) {
        if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Profile picture upload failed.';
        } else {
            $tmp = $_FILES['profile_picture']['tmp_name'];
            $mime = mime_content_type($tmp);
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            if (!isset($allowed[$mime])) {
                $errors[] = 'Profile picture must be JPG, PNG, or WEBP.';
            } elseif ((int) $_FILES['profile_picture']['size'] > 2 * 1024 * 1024) {
                $errors[] = 'Profile picture must be 2 MB or smaller.';
            } else {
                $fileName = 'tutor_' . current_user()['id'] . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
                $target = __DIR__ . '/../uploads/tutors/' . $fileName;
                if (!move_uploaded_file($tmp, $target)) {
                    $errors[] = 'Could not save profile picture.';
                } else {
                    $picturePath = 'uploads/tutors/' . $fileName;
                }
            }
        }
    }

    if (!$errors) {
        if ($profile) {
            $stmt = $pdo->prepare('
                UPDATE tutor_profiles
                SET full_name = ?, phone = ?, gender = ?, university = ?, department = ?, academic_year = ?, cgpa = ?, teaching_experience = ?, preferred_subjects = ?, preferred_areas = ?, profile_picture = ?, bio = ?
                WHERE user_id = ?
            ');
            $stmt->execute([
                $values['full_name'], $values['phone'], $values['gender'], $values['university'], $values['department'],
                $values['academic_year'], $values['cgpa'] === '' ? null : (float) $values['cgpa'], $values['teaching_experience'],
                $values['preferred_subjects'], $values['preferred_areas'], $picturePath, $values['bio'], current_user()['id'],
            ]);
            flash('success', 'Tutor profile updated.');
        } else {
            $stmt = $pdo->prepare('
                INSERT INTO tutor_profiles (user_id, full_name, phone, gender, university, department, academic_year, cgpa, teaching_experience, preferred_subjects, preferred_areas, profile_picture, bio)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                current_user()['id'], $values['full_name'], $values['phone'], $values['gender'], $values['university'],
                $values['department'], $values['academic_year'], $values['cgpa'] === '' ? null : (float) $values['cgpa'],
                $values['teaching_experience'], $values['preferred_subjects'], $values['preferred_areas'], $picturePath, $values['bio'],
            ]);
            flash('success', 'Tutor profile created.');
        }
        redirect_to('/tutor/profile.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <h1 class="h3 mb-4">Tutor Profile</h1>
        <?php render_flash_messages(); ?>
        <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
        <div class="card table-card">
            <div class="card-body p-4">
                <form method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Full Name</label><input name="full_name" class="form-control" value="<?= e($values['full_name']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= e($values['phone']) ?>" required></div>
                        <div class="col-md-4"><label class="form-label">Gender</label><select name="gender" class="form-select" required><?php foreach (['Male','Female','Other'] as $gender): ?><option <?= $values['gender'] === $gender ? 'selected' : '' ?>><?= e($gender) ?></option><?php endforeach; ?></select></div>
                        <div class="col-md-8"><label class="form-label">University</label><input name="university" class="form-control" value="<?= e($values['university']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Department</label><input name="department" class="form-control" value="<?= e($values['department']) ?>" required></div>
                        <div class="col-md-3"><label class="form-label">Academic Year</label><input name="academic_year" class="form-control" value="<?= e($values['academic_year']) ?>" required></div>
                        <div class="col-md-3"><label class="form-label">CGPA</label><input type="number" step="0.01" min="0" max="4" name="cgpa" class="form-control" value="<?= e((string) $values['cgpa']) ?>"></div>
                        <div class="col-md-6"><label class="form-label">Preferred Subjects</label><input name="preferred_subjects" class="form-control" value="<?= e($values['preferred_subjects']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Preferred Areas</label><input name="preferred_areas" class="form-control" value="<?= e($values['preferred_areas']) ?>" required></div>
                        <div class="col-12"><label class="form-label">Teaching Experience</label><textarea name="teaching_experience" rows="4" class="form-control" required><?= e($values['teaching_experience']) ?></textarea></div>
                        <div class="col-12"><label class="form-label">Bio</label><textarea name="bio" rows="4" class="form-control"><?= e($values['bio']) ?></textarea></div>
                        <div class="col-md-6"><label class="form-label">Profile Picture</label><input type="file" name="profile_picture" class="form-control" accept="image/jpeg,image/png,image/webp"></div>
                    </div>
                    <button class="btn btn-primary mt-4"><?= $profile ? 'Update Profile' : 'Create Profile' ?></button>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
