<?php
require_once dirname(__DIR__) . '/config/database.php';
$pageTitle = 'Edit User';
$activePage = 'users';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/User.php';
require_once ROOT_PATH . '/classes/Upload.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/admin-header.php';

$id = intval($_GET['id'] ?? 0);
$userModel = new User();
$user = $userModel->getById($id);

if (!$user) {
    set_flash('error', 'User not found.');
    redirect(BASE_URL . '/admin/users.php');
}

$upload = new Upload();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $role = $_POST['role'] ?? 'user';

    if ($name === '' || strlen($name) < 2) {
        $errors[] = 'Name must be at least 2 characters.';
    }
    if (!in_array($role, ['admin', 'user'])) {
        $role = 'user';
    }

    if (empty($errors)) {
        $userModel->updateName($id, $name);
        $userModel->updateRole($id, $role);

        if (!empty($_FILES['profile_image']['name'])) {
            $result = $upload->handle($_FILES['profile_image'], UPLOAD_PATH . 'profiles/');
            if ($result['success']) {
                if ($user['profile_image'] && file_exists($user['profile_image'])) {
                    unlink($user['profile_image']);
                }
                $userModel->updateImage($id, $result['path']);
            } else {
                $errors[] = $result['message'];
            }
        }

        if (empty($errors)) {
            set_flash('success', 'User updated.');
            redirect(BASE_URL . '/admin/users.php');
        }
    }
}

$user = $userModel->getById($id);
?>

<div class="admin-card" style="max-width:560px">
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px">
        <?php if ($user['profile_image']): ?>
            <img src="<?= e($user['profile_image']) ?>" alt="" class="avatar-lg">
        <?php else: ?>
            <div class="avatar-lg avatar-fallback"><?= get_initials($user['name']) ?></div>
        <?php endif; ?>
        <div>
            <div style="font-weight:600"><?= e($user['name']) ?></div>
            <div class="text-muted"><?= e($user['email']) ?></div>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="form-errors" style="margin-bottom:20px">
            <?php foreach ($errors as $err): ?>
                <p><?= e($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-group">
            <label class="form-label" for="name">Name</label>
            <input type="text" id="name" name="name" class="form-input" value="<?= e($user['name']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="role">Role</label>
            <select id="role" name="role" class="form-select">
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="profile_image">Profile Image</label>
            <input type="file" id="profile_image" name="profile_image" class="form-input" accept="image/*">
            <small class="form-hint">Leave empty to keep current image.</small>
        </div>

        <div class="form-actions">
            <a href="<?= BASE_URL ?>/admin/users.php" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>