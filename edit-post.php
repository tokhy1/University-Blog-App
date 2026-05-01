<?php
$pageTitle = 'Edit Post';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/Post.php';
require_once ROOT_PATH . '/classes/Category.php';
require_once ROOT_PATH . '/classes/Tag.php';
require_once ROOT_PATH . '/classes/Upload.php';
require_once ROOT_PATH . '/includes/functions.php';

Auth::requireLogin();

$id = intval($_GET['id'] ?? 0);
$postModel = new Post();
$post = $postModel->getById($id);

if (!$post || $post['user_id'] !== Auth::id()) {
    set_flash('error', 'Post not found or you do not have permission.');
    redirect(BASE_URL . '/my-posts.php');
}

$catModel = new Category();
$tagModel = new Tag();
$upload = new Upload();
$categories = $catModel->getAll();
$postTags = $postModel->getTags($id);
$tagsString = implode(', ', array_column($postTags, 'name'));
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim($_POST['title'] ?? '');
    $categoryId = intval($_POST['category_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? $post['status'];
    $tagsInput = trim($_POST['tags'] ?? '');
    $removeThumb = isset($_POST['remove_thumbnail']);

    if ($title === '') $errors[] = 'Title is required.';
    if ($categoryId === 0) $errors[] = 'Select a category.';
    if ($content === '') $errors[] = 'Content is required.';
    if (!in_array($status, ['published', 'draft'])) $status = $post['status'];

    $thumbnailPath = null;
    if ($removeThumb) {
        if ($post['thumbnail'] && file_exists($post['thumbnail'])) unlink($post['thumbnail']);
        $thumbnailPath = '';
    } elseif (!empty($_FILES['thumbnail']['name'])) {
        $result = $upload->handle($_FILES['thumbnail'], UPLOAD_PATH . 'thumbnails/');
        if (!$result['success']) {
            $errors[] = $result['message'];
        } else {
            if ($post['thumbnail'] && file_exists($post['thumbnail'])) unlink($post['thumbnail']);
            $thumbnailPath = $result['path'];
        }
    }

    if (empty($errors)) {
        $postModel->update($id, $categoryId, $title, $content, $thumbnailPath, $status);

        if ($tagsInput !== '') {
            $tagNames = array_map('trim', explode(',', $tagsInput));
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                if ($tagName !== '') $tagIds[] = $tagModel->findOrCreate($tagName);
            }
            $postModel->setTags($id, $tagIds);
        } else {
            $postModel->setTags($id, []);
        }

        set_flash('success', 'Post updated.');
        redirect(BASE_URL . '/my-posts.php');
    }
}

// Reload in case of errors
if (!empty($errors)) {
    $post = $postModel->getById($id);
    $tagsString = old('tags');
}

require_once ROOT_PATH . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="page-header">
            <h1>Edit Post</h1>
            <a href="<?= BASE_URL ?>/my-posts.php" class="btn btn-outline">Back to My Posts</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="form-errors">
                <?php foreach ($errors as $err): ?>
                    <p><?= e($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data" class="post-form">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="title">Title</label>
                <input type="text" id="title" name="title" class="form-input"
                    value="<?= e(!empty($errors) ? old('title') : $post['title']) ?>"
                    placeholder="Give your post a title" required>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1">
                    <label class="form-label" for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <option value="">Select category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= (!empty($errors) ? old('category_id') : $post['category_id']) == $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex:1">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="draft" <?= (!empty($errors) ? old('status') : $post['status']) === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= (!empty($errors) ? old('status') : $post['status']) === 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Current Thumbnail</label>
                <?php if ($post['thumbnail']): ?>
                    <div class="current-thumbnail">
                        <img src="<?= e($post['thumbnail']) ?>" alt="">
                        <label class="checkbox-label" style="margin-top:6px">
                            <input type="checkbox" name="remove_thumbnail">
                            <span>Remove thumbnail</span>
                        </label>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No thumbnail set.</p>
                <?php endif; ?>
                <label class="form-label" for="thumbnail" style="margin-top:8px">Replace Thumbnail</label>
                <input type="file" id="thumbnail" name="thumbnail" class="form-input" accept="image/*">
                <small class="form-hint">JPG, PNG, GIF or WebP. Max 2MB.</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="content">Content <span class="text-muted">(Markdown)</span></label>
                <div class="editor-toolbar">
                    <button type="button" onclick="insertMd('**','**')" title="Bold"><strong>B</strong></button>
                    <button type="button" onclick="insertMd('*','*')" title="Italic"><em>I</em></button>
                    <button type="button" onclick="insertMd('## ','')" title="Heading">H2</button>
                    <button type="button" onclick="insertMd('### ','')" title="Subheading">H3</button>
                    <button type="button" onclick="insertMd('[','](url)')" title="Link">Link</button>
                    <button type="button" onclick="insertMd('![alt](',')')" title="Image">Img</button>
                    <button type="button" onclick="insertMd('`','`')" title="Code">Code</button>
                    <button type="button" onclick="insertMd('\n```\n','\n```\n')" title="Code Block">Block</button>
                    <button type="button" onclick="insertMd('- ','')" title="List">List</button>
                    <button type="button" onclick="insertMd('> ','')" title="Quote">Quote</button>
                    <button type="button" onclick="triggerImageUpload()" title="Upload Image">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <path d="M21 15l-5-5L5 21" />
                        </svg>
                        Upload
                    </button>
                </div>
                <textarea id="content" name="content" class="form-textarea editor-textarea" rows="16"
                    placeholder="Write your post content here..." required><?= e(!empty($errors) ? old('content') : $post['content']) ?></textarea>
                <input type="file" id="content-image-upload" name="content_image" class="hidden" accept="image/*"
                    onchange="uploadContentImage(this)">
            </div>

            <div class="form-group">
                <label class="form-label" for="tags">Tags <span class="text-muted">(comma-separated)</span></label>
                <input type="text" id="tags" name="tags" class="form-input"
                    value="<?= e($tagsString) ?>" placeholder="php, web development, tutorial">
            </div>

            <div class="form-actions">
                <button type="submit" name="status" value="draft" class="btn btn-outline">Save Draft</button>
                <button type="submit" name="status" value="published" class="btn btn-primary">Publish</button>
            </div>
        </form>
    </div>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>