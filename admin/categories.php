<?php
$pageTitle = 'Categories';
$activePage = 'categories';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/Category.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/admin-header.php';

$catModel = new Category();
$errors = [];

if (isset($_POST['action'])) {
    verify_csrf();

    if ($_POST['action'] === 'create') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $errors[] = 'Category name is required.';
        } else {
            try {
                $catModel->create($name);
                set_flash('success', 'Category created.');
                redirect(BASE_URL . '/admin/categories.php');
            } catch (PDOException $e) {
                $errors[] = 'This category name already exists.';
            }
        }
    }

    if ($_POST['action'] === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $errors[] = 'Category name is required.';
        } else {
            try {
                $catModel->update($id, $name);
                set_flash('success', 'Category updated.');
                redirect(BASE_URL . '/admin/categories.php');
            } catch (PDOException $e) {
                $errors[] = 'This category name already exists.';
            }
        }
    }

    if ($_POST['action'] === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        $deleted = $catModel->delete($id);
        if ($deleted) {
            set_flash('success', 'Category deleted.');
        } else {
            set_flash('error', 'Cannot delete category that has posts.');
        }
        redirect(BASE_URL . '/admin/categories.php');
    }
}

$categories = $catModel->getAll();
?>

<?php if (!empty($errors)): ?>
    <div class="form-errors" style="margin-bottom:20px">
        <?php foreach ($errors as $err): ?>
            <p><?= e($err) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="admin-card" style="margin-bottom:24px">
    <h3 style="margin-bottom:16px">Add New Category</h3>
    <form method="POST" action="" class="inline-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create">
        <input type="text" name="name" class="form-input" placeholder="Category name" required style="max-width:300px">
        <button type="submit" class="btn btn-primary btn-sm">Add</button>
    </form>
</div>

<div class="admin-card">
    <h3 style="margin-bottom:16px">All Categories</h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Posts</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr id="cat-row-<?= $cat['id'] ?>">
                        <td>
                            <span class="cat-name-display"><?= e($cat['name']) ?></span>
                            <form method="POST" action="" class="inline-edit-form" id="cat-edit-<?= $cat['id'] ?>" style="display:none">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                <input type="text" name="name" class="form-input" value="<?= e($cat['name']) ?>" required style="max-width:200px">
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                <button type="button" class="btn btn-sm btn-outline" onclick="document.getElementById('cat-edit-<?= $cat['id'] ?>').style.display='none';document.querySelector('#cat-row-<?= $cat['id'] ?> .cat-name-display').style.display=''">Cancel</button>
                            </form>
                        </td>
                        <td class="text-muted"><?= e($cat['slug']) ?></td>
                        <td><?= $catModel->getPostCount($cat['id']) ?></td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-outline" onclick="document.querySelector('#cat-row-<?= $cat['id'] ?> .cat-name-display').style.display='none';document.getElementById('cat-edit-<?= $cat['id'] ?>').style.display='inline-flex'">Edit</button>
                            <form method="POST" action="" style="display:inline"
                                onsubmit="return confirm('Delete this category?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="4" class="text-muted" style="text-align:center;padding:32px">No categories yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>