<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'About Us - Tuition Media System';
require_once __DIR__ . '/includes/header.php';

$values = [
    ['icon' => '✓', 'title' => 'Clarity', 'copy' => 'Simple workflows for students, tutors, and administrators.'],
    ['icon' => '★', 'title' => 'Trust', 'copy' => 'Transparent profiles, applications, and decisions.'],
    ['icon' => '↗', 'title' => 'Momentum', 'copy' => 'Fast movement from discovery to successful tuition matching.'],
    ['icon' => '◇', 'title' => 'Craft', 'copy' => 'Clean interfaces backed by a reliable database foundation.'],
];

$features = [
    ['title' => 'Role-Based Dashboards', 'copy' => 'Focused panels for students, tutors, and admins.'],
    ['title' => 'Smart Applications', 'copy' => 'Students can apply and track application status with ease.'],
    ['title' => 'Tutor Discovery', 'copy' => 'Browse tutor profiles by subject, area, and university.'],
    ['title' => 'Admin Control', 'copy' => 'Manage users, posts, profiles, and applications in one place.'],
];

$leaders = [
    ['name' => 'Nadia Rahman', 'role' => 'Chief Executive Officer', 'initials' => 'NR'],
    ['name' => 'Arif Hasan', 'role' => 'Head of Product', 'initials' => 'AH'],
    ['name' => 'Samira Chowdhury', 'role' => 'Engineering Lead', 'initials' => 'SC'],
];

$stats = [
    ['value' => '1,200+', 'label' => 'Learner connections'],
    ['value' => '340+', 'label' => 'Tutor profiles'],
    ['value' => '98%', 'label' => 'Workflow completion'],
    ['value' => '24/7', 'label' => 'Platform access'],
];
?>
<main class="about-bootstrap">
    <section class="about-bs-hero py-5 py-lg-6">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="badge rounded-pill text-bg-light border px-3 py-2 mb-3">About Tuition Media System</span>
                    <h1 class="display-2 fw-bold lh-1 mb-4">Modern tuition management for ambitious learners.</h1>
                    <p class="lead text-secondary mb-4">
                        TMS helps students discover tuition opportunities, tutors build credibility, and education teams
                        manage applications, schedules, and decisions from one professional platform.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a class="btn btn-primary btn-lg rounded-pill px-4" href="<?= e(url('/tuition/index.php')) ?>">Explore Tuitions</a>
                        <a class="btn btn-outline-primary btn-lg rounded-pill px-4" href="<?= e(url('/tutors/index.php')) ?>">Browse Tutors</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-header bg-white border-0">
                            <div class="d-flex gap-2">
                                <span class="about-dot bg-danger"></span>
                                <span class="about-dot bg-warning"></span>
                                <span class="about-dot bg-success"></span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="p-4 rounded-4 text-black about-gradient-card mb-3">
                                <div class="text-black-50 small text-uppercase fw-semibold">Application Flow</div>
                                <div class="display-5 fw-bold">2.8x</div>
                                <div>faster review cycles</div>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-3 rounded-4 bg-light border h-100">
                                        <div class="text-secondary small">Profiles</div>
                                        <div class="h3 fw-bold mb-0">340+</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-4 bg-light border h-100">
                                        <div class="text-secondary small">Posts</div>
                                        <div class="h3 fw-bold mb-0">Live</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-primary rounded-pill mb-3">Company Story</span>
                    <h2 class="display-6 fw-bold">Created to make education access feel effortless.</h2>
                    <p class="text-secondary fs-5">We built TMS to replace scattered tuition communication with one clear, trusted, and scalable workflow.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <span class="badge text-bg-light border mb-3">2024</span>
                            <h3 class="h5 fw-bold">Problem identified</h3>
                            <p class="text-secondary mb-0">Students needed a clearer way to find tuition opportunities and follow decisions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <span class="badge text-bg-light border mb-3">2025</span>
                            <h3 class="h5 fw-bold">Platform launched</h3>
                            <p class="text-secondary mb-0">Role-based dashboards brought students, tutors, and admins into one system.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <span class="badge text-bg-light border mb-3">2026</span>
                            <h3 class="h5 fw-bold">Scaling the network</h3>
                            <p class="text-secondary mb-0">We are improving matching, review tools, and the learning marketplace experience.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-5">
                            <h2 class="fw-bold">Mission</h2>
                            <p class="text-secondary fs-5 mb-0">Make quality tuition easier to discover, manage, and trust through simple technology.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-5">
                            <h2 class="fw-bold">Vision</h2>
                            <p class="text-secondary fs-5 mb-0">Become the dependable digital layer for local education networks and learning communities.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <span class="badge text-bg-primary rounded-pill mb-3">Core Values</span>
                    <h2 class="display-6 fw-bold mb-0">Principles that shape the product.</h2>
                </div>
            </div>
            <div class="row g-4">
                <?php foreach ($values as $value): ?>
                    <div class="col-md-6 col-xl-3">
                        <div class="card h-100 border-0 shadow-sm rounded-4 about-hover">
                            <div class="card-body p-4">
                                <div class="about-icon mb-4"><?= e($value['icon']) ?></div>
                                <h3 class="h5 fw-bold"><?= e($value['title']) ?></h3>
                                <p class="text-secondary mb-0"><?= e($value['copy']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-primary rounded-pill mb-3">What Makes Us Different</span>
                    <h2 class="display-6 fw-bold">Purpose-built for tuition workflows.</h2>
                </div>
            </div>
            <div class="row g-4">
                <?php foreach ($features as $feature): ?>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-4 about-hover">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="about-check">✓</div>
                                    <div>
                                        <h3 class="h5 fw-bold"><?= e($feature['title']) ?></h3>
                                        <p class="text-secondary mb-0"><?= e($feature['copy']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- <section class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-primary rounded-pill mb-3">Leadership</span>
                    <h2 class="display-6 fw-bold">Small team, serious standards.</h2>
                </div>
            </div>
            <div class="row g-4">
                <?php foreach ($leaders as $leader): ?>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 text-center about-hover">
                            <div class="card-body p-4">
                                <div class="about-avatar mx-auto mb-3"><?= e($leader['initials']) ?></div>
                                <h3 class="h5 fw-bold"><?= e($leader['name']) ?></h3>
                                <p class="text-secondary"><?= e($leader['role']) ?></p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-sm btn-outline-primary rounded-pill" href="#">LinkedIn</a>
                                    <a class="btn btn-sm btn-outline-primary rounded-pill" href="#">X</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section> -->

    <section class="py-5 bg-dark text-white">
        <div class="container">
            <div class="row g-4 text-center">
                <?php foreach ($stats as $stat): ?>
                    <div class="col-6 col-lg-3">
                        <div class="p-4 rounded-4 border border-secondary h-100">
                            <div class="display-5 fw-bold"><?= e($stat['value']) ?></div>
                            <p class="text-white-50 mb-0"><?= e($stat['label']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <span class="badge text-bg-primary rounded-pill mb-3">Culture</span>
                    <h2 class="display-6 fw-bold">Calm execution, high ownership, learner-first decisions.</h2>
                    <p class="text-secondary fs-5">We work in focused cycles, keep the interface quiet, and make every product decision around real education outcomes.</p>
                </div>
                <div class="col-lg-6">
                    <div class="list-group shadow-sm rounded-4 overflow-hidden">
                        <div class="list-group-item p-4 fw-semibold">Research before features</div>
                        <div class="list-group-item p-4 fw-semibold">Simple flows over busy screens</div>
                        <div class="list-group-item p-4 fw-semibold">Reliable systems over flashy demos</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-primary rounded-pill mb-3">Testimonials</span>
                    <h2 class="display-6 fw-bold">Trusted by education operators.</h2>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4"><div class="card h-100 border-0 shadow-sm rounded-4"><div class="card-body p-4"><p class="fs-5">"TMS gave our tuition process structure and made applications easier to review."</p><strong>Farhana Islam</strong><div class="text-secondary">Academic Coordinator</div></div></div></div>
                <div class="col-md-4"><div class="card h-100 border-0 shadow-sm rounded-4"><div class="card-body p-4"><p class="fs-5">"It removes the back-and-forth that used to slow down tutor matching."</p><strong>Mahmud Karim</strong><div class="text-secondary">Learning Center Director</div></div></div></div>
                <div class="col-md-4"><div class="card h-100 border-0 shadow-sm rounded-4"><div class="card-body p-4"><p class="fs-5">"Clean dashboards, simple records, and faster decisions for our team."</p><strong>Priya Sen</strong><div class="text-secondary">Operations Lead</div></div></div></div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="about-cta text-black text-center rounded-4 shadow-lg p-5">
                <h2 class="display-6 fw-bold">Ready to build a better tuition network?</h2>
                <p class="lead text-black-50 mx-auto" style="max-width: 720px;">Explore opportunities, meet tutors, and manage the learning journey from one modern platform.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                    <a class="btn btn-dark btn-lg rounded-pill px-4" href="<?= e(url('/auth/register.php')) ?>">Get Started</a>
                    <a class="btn btn-outline-dark btn-lg rounded-pill px-4" href="<?= e(url('/login')) ?>">Sign In</a>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
