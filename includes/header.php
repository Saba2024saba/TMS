<?php
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'Tuition Management System';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(url('/assets/css/style.css')) ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-semibold" href="<?= e(role_home()) ?>">TMS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="topNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="<?= e(url('/tuition/index.php')) ?>">Tuitions</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(url('/tutors/index.php')) ?>">Tutors</a></li>
                <?php if (current_user() && current_user()['role'] !== 'student'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= e(url('/tuition/create.php')) ?>">Post Tuition</a></li>
                <?php endif; ?>
                <?php if (current_user()): ?>
                    <li class="nav-item text-white-50 small px-lg-2">
                        <?= e(current_user()['name']) ?> (<?= e(current_user()['role']) ?>)
                    </li>
                    <li class="nav-item"><a class="btn btn-light btn-sm" href="<?= e(url('/auth/logout.php')) ?>">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-light btn-sm" href="<?= e(url('/login')) ?>">Login</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="<?= e(url('/auth/register.php')) ?>">Registration</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>


