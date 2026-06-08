<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('admin');

$active = 'schedules';
$pageTitle = 'Schedules - TMS';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'create') {
            $stmt = $pdo->prepare('CALL assign_schedule_with_time(?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                (int) $_POST['tutor_id'],
                (int) $_POST['student_id'],
                (int) $_POST['subject_id'],
                $_POST['class_date'],
                $_POST['start_time'],
                $_POST['end_time'],
            ]);
            $stmt->closeCursor();
        } elseif ($action === 'update') {
            $stmt = $pdo->prepare('UPDATE schedules SET tutor_id = ?, student_id = ?, subject_id = ?, class_date = ?, start_time = ?, end_time = ? WHERE id = ?');
            $stmt->execute([
                (int) $_POST['tutor_id'],
                (int) $_POST['student_id'],
                (int) $_POST['subject_id'],
                $_POST['class_date'],
                $_POST['start_time'],
                $_POST['end_time'],
                (int) $_POST['id'],
            ]);
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM schedules WHERE id = ?');
            $stmt->execute([(int) $_POST['id']]);
        }
        redirect_to('/admin/schedules.php');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$tutors = db_fetch_all($pdo, '
    SELECT t.id, u.name, s.subject_name
    FROM tutors t
    JOIN users u ON t.user_id = u.id
    JOIN subjects s ON t.subject_id = s.id
    ORDER BY u.name
');
$students = db_fetch_all($pdo, '
    SELECT st.id, u.name, st.class_name
    FROM students st
    JOIN users u ON st.user_id = u.id
    ORDER BY u.name
');
$subjects = db_fetch_all($pdo, 'SELECT id, subject_name FROM subjects ORDER BY subject_name');
$schedules = db_fetch_all($pdo, 'SELECT * FROM view_schedule_details ORDER BY class_date DESC, start_time DESC');

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Schedules</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSchedule">Assign Schedule</button>
        </div>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Date</th><th>Time</th><th>Tutor</th><th>Student</th><th>Subject</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?= e($schedule['class_date']) ?></td>
                            <td><?= e(substr($schedule['start_time'], 0, 5) . ' - ' . substr($schedule['end_time'], 0, 5)) ?></td>
                            <td><?= e($schedule['tutor_name']) ?></td>
                            <td><?= e($schedule['student_name']) ?></td>
                            <td><?= e($schedule['subject_name']) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSchedule<?= (int) $schedule['schedule_id'] ?>">Edit</button>
                                <form method="post" class="d-inline" onsubmit="return confirm('Delete this schedule?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $schedule['schedule_id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <div class="modal fade" id="editSchedule<?= (int) $schedule['schedule_id'] ?>" tabindex="-1">
                            <div class="modal-dialog"><div class="modal-content"><form method="post">
                                <div class="modal-header"><h5 class="modal-title">Edit Schedule</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= (int) $schedule['schedule_id'] ?>">
                                    <?php include __DIR__ . '/../includes/schedule_form_fields.php'; ?>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
                            </form></div></div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$schedules): ?><tr><td colspan="6" class="text-center text-muted py-4">No schedules found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="addSchedule" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content"><form method="post">
        <div class="modal-header"><h5 class="modal-title">Assign Schedule</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="create">
            <?php $schedule = null; include __DIR__ . '/../includes/schedule_form_fields.php'; ?>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
    </form></div></div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

