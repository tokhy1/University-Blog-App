<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'My Profile';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/User.php';
require_once ROOT_PATH . '/classes/Upload.php';
require_once ROOT_PATH . '/includes/functions.php';

Auth::requireLogin();
require_once ROOT_PATH . '/includes/header.php';

$userModel = new User();
$upload = new Upload();
$user = $userModel->getById(Auth::id());
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    if ($name === '' || strlen($name) < 2) {
        $errors[] = 'Name must be at least 2 characters.';
    }

    if (empty($errors)) {
        $userModel->updateName(Auth::id(), $name);
        $_SESSION['user_name'] = $name;

        if (!empty($_FILES['profile_image']['name'])) {
            $result = $upload->handle($_FILES['profile_image'], UPLOAD_PATH . 'profiles/');
            if ($result['success']) {
                if ($user['profile_image'] && file_exists($user['profile_image'])) {
                    unlink($user['profile_image']);
                }
                $userModel->updateImage(Auth::id(), $result['path']);
                $_SESSION['user_image'] = $result['path'];
            } else {
                $errors[] = $result['message'];
            }
        }

        if (empty($errors)) {
            set_flash('success', 'Profile updated.');
            redirect(BASE_URL . '/profile.php');
        }
    }
}

$user = $userModel->getById(Auth::id());
?>

<section class="section">
    <div class="container">
        <div class="profile-page">
            <div class="profile-sidebar">
                <div class="profile-avatar-wrapper">
                    <?php if ($user['profile_image']): ?>
                        <img src="<?= e($user['profile_image']) ?>" alt="" class="profile-avatar">
                    <?php else: ?>
                        <div class="profile-avatar profile-avatar-fallback"><?= get_initials($user['name']) ?></div>
                    <?php endif; ?>
                </div>
                <h2><?= e($user['name']) ?></h2>
                <p class="text-muted"><?= e($user['email']) ?></p>
                <span class="role-badge"><?= ucfirst($user['role']) ?></span>
                <p class="text-muted" style="margin-top:8px">Joined <?= date('M d, Y', strtotime($user['created_at'])) ?></p>
            </div>

            <div class="profile-form-wrapper">
                <h3>Edit Profile</h3>

                <?php if (!empty($errors)): ?>
                    <div class="form-errors">
                        <?php foreach ($errors as $err): ?>
                            <p><?= e($err) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-input"
                            value="<?= e($user['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="profile_image">Profile Image</label>
                        <input type="file" id="profile_image" name="profile_image" class="form-input" accept="image/*">
                        <small class="form-hint">JPG, PNG, GIF or WebP. Max 2MB.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>

                <div class="profile-info-block">
                    <h4>Account Info</h4>
                    <p><strong>Email:</strong> <?= e($user['email']) ?> <span class="text-muted">(cannot be changed)</span></p>
                    <p><strong>Posts:</strong> <?= $userModel->getPostCount($user['id']) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>