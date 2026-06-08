<?php
require_once __DIR__ . '/auth.php';
$active = $active ?? '';
$user = current_user();
$role = $user['role'] ?? '';

$menus = [
    'admin' => [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'url' => url('/admin/dashboard.php')],
        ['key' => 'users', 'label' => 'Users', 'url' => url('/admin/users.php')],
        ['key' => 'subjects', 'label' => 'Subjects', 'url' => url('/admin/subjects.php')],
        ['key' => 'tutors', 'label' => 'Tutors', 'url' => url('/admin/tutors.php')],
        ['key' => 'tutor_profiles', 'label' => 'Tutor Profiles', 'url' => url('/admin/tutor_profiles.php')],
        ['key' => 'tuition_posts', 'label' => 'Tuition Posts', 'url' => url('/admin/tuition_posts.php')],
        ['key' => 'tuition_applications', 'label' => 'Applications', 'url' => url('/admin/tuition_applications.php')],
        ['key' => 'students', 'label' => 'Students', 'url' => url('/admin/students.php')],
        ['key' => 'schedules', 'label' => 'Schedules', 'url' => url('/admin/schedules.php')],
    ],
    'tutor' => [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'url' => url('/tutor/dashboard.php')],
        ['key' => 'profile', 'label' => 'Tutor Profile', 'url' => url('/tutor/profile.php')],
        ['key' => 'applications', 'label' => 'Applications', 'url' => url('/tutor/applications.php')],
        ['key' => 'schedules', 'label' => 'My Schedules', 'url' => url('/tutor/schedules.php')],
    ],
    'student' => [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'url' => url('/student/dashboard.php')],
        ['key' => 'tuition_posts', 'label' => 'Tuition Posts', 'url' => url('/tuition/index.php')],
        ['key' => 'applications', 'label' => 'My Applications', 'url' => url('/student/applications.php')],
        ['key' => 'schedules', 'label' => 'My Schedules', 'url' => url('/student/schedules.php')],
    ],
];
?>
<aside class="sidebar border-end bg-white">
    <div class="p-3 border-bottom">
        <div class="fw-semibold">Navigation</div>
        <div class="text-muted small"><?= e(ucfirst($role)) ?> panel</div>
    </div>
    <div class="list-group list-group-flush">
        <?php foreach (($menus[$role] ?? []) as $item): ?>
            <a class="list-group-item list-group-item-action <?= $active === $item['key'] ? 'active' : '' ?>"
               href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
        <?php endforeach; ?>
    </div>
</aside>
