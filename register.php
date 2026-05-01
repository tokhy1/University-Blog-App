<?php
$pageTitle = 'Create Account';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/header.php';

$auth = new Auth();
if ($auth->isLoggedIn()) {
    redirect(BASE_URL . '/index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || strlen($name) < 2) $errors[] = 'Name must be at least 2 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $result = $auth->register($name, $email, $password);
        if ($result['success']) {
            set_flash('success', 'Account created. You can now log in.');
            redirect(BASE_URL . '/login.php');
        } else {
            $errors[] = $result['message'];
        }
    }
}
?>

<div class="auth-page">
    <div class="auth-card">
        <h2>Create your account</h2>
        <p class="auth-subtitle">Join the community and start sharing your thoughts.</p>

        <?php if (!empty($errors)): ?>
            <div class="form-errors">
                <?php foreach ($errors as $err): ?>
                    <p><?= e($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-input"
                    value="<?= e(old('name')) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-input"
                    value="<?= e(old('email')) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Create Account</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="<?= BASE_URL ?>/login.php">Sign in</a></p>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>