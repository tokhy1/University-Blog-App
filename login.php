<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Sign In';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/header.php';

$auth = new Auth();
if ($auth->isLoggedIn()) {
    if ($auth->isAdmin()) redirect(BASE_URL . '/admin/');
    redirect(BASE_URL . '/index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if ($email === '' || $password === '') {
        $errors[] = 'Enter both email and password.';
    }

    if (empty($errors)) {
        $result = $auth->login($email, $password, $remember);
        if ($result['success']) {
            if ($result['role'] === 'admin') {
                redirect(BASE_URL . '/admin/');
            }
            redirect(BASE_URL . '/index.php');
        } else {
            $errors[] = $result['message'];
        }
    }
}
?>

<div class="auth-page">
    <div class="auth-card">
        <h2>Welcome back</h2>
        <p class="auth-subtitle">Sign in to your account to continue.</p>

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
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-input"
                    value="<?= e(old('email')) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>

            <div class="form-row form-row-between">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Sign In</button>
        </form>

        <p class="auth-footer">Don't have an account? <a href="<?= BASE_URL ?>/register.php">Create one</a></p>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?> 