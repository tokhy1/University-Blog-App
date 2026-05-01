<?php
require_once dirname(__DIR__) . '/config/database.php';
$pageTitle = 'Users';
$activePage = 'users';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/User.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/admin-header.php';

$userModel = new User();
$page = max(1, intval($_GET['page'] ?? 1));
$users = $userModel->getAll($page, 20);
$total = $userModel->getTotal();
?>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Posts</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <div class="user-cell">
                            <?php if ($user['profile_image']): ?>
                                <img src="<?= e($user['profile_image']) ?>" alt="" class="avatar-sm">
                            <?php else: ?>
                                <div class="avatar-sm avatar-fallback"><?= get_initials($user['name']) ?></div>
                            <?php endif; ?>
                            <?= e($user['name']) ?>
                        </div>
                    </td>
                    <td class="text-muted"><?= e($user['email']) ?></td>
                    <td>
                        <span class="role-badge <?= $user['role'] === 'admin' ? 'role-admin' : 'role-user' ?>">
                            <?= ucfirst($user['role']) ?>
                        </span>
                    </td>
                    <td><?= $userModel->getPostCount($user['id']) ?></td>
                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                    <td class="table-actions">
                        <a href="<?= BASE_URL ?>/admin/user-edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                        <?php if ($user['role'] !== 'admin'): ?>
                            <form method="POST" action="<?= BASE_URL ?>/admin/user-delete.php" style="display:inline"
                                onsubmit="return confirm('Delete this user and all their posts?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= paginate($page, $total, 20, BASE_URL . '/admin/users.php?page=') ?>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>