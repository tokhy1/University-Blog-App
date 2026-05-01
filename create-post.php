<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Write a Post';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/Post.php';
require_once ROOT_PATH . '/classes/Category.php';
require_once ROOT_PATH . '/classes/Tag.php';
require_once ROOT_PATH . '/classes/Upload.php';
require_once ROOT_PATH . '/includes/functions.php';

Auth::requireLogin();
require_once ROOT_PATH . '/includes/header.php';

$catModel = new Category();
$tagModel = new Tag();
$upload = new Upload();
$categories = $catModel->getAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim($_POST['title'] ?? '');
    $categoryId = intval($_POST['category_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['submit_action'] ?? $_POST['status'] ?? 'draft';
    $tagsInput = trim($_POST['tags'] ?? '');

    if ($title === '') $errors[] = 'Title is required.';
    if ($categoryId === 0) $errors[] = 'Select a category.';
    if ($content === '') $errors[] = 'Content is required.';
    if (!in_array($status, ['published', 'draft'])) $status = 'draft';

    $thumbnailPath = null;
    if (!empty($_FILES['thumbnail']['name'])) {
        $result = $upload->handle($_FILES['thumbnail'], UPLOAD_PATH . 'thumbnails/');
        if (!$result['success']) {
            $errors[] = $result['message'];
        } else {
            $thumbnailPath = $result['path'];
        }
    }

    if (empty($errors)) {
        $postModel = new Post();
        $postId = $postModel->create(Auth::id(), $categoryId, $title, $content, $thumbnailPath, $status);

        if ($tagsInput !== '') {
            $tagNames = array_map('trim', explode(',', $tagsInput));
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                if ($tagName !== '') $tagIds[] = $tagModel->findOrCreate($tagName);
            }
            $postModel->setTags($postId, $tagIds);
        }

        set_flash('success', 'Post created successfully.');
        redirect(BASE_URL . '/my-posts.php');
    }
}
?>

<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <a href="<?= BASE_URL ?>/my-posts.php" class="breadcrumb-back">&larr; My Posts</a>
                <h1>New Post</h1>
            </div>
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
                    value="<?= e(old('title')) ?>" placeholder="Give your post a title" required>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1">
                    <label class="form-label" for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <option value="">Select category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex:1">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="thumbnail">Thumbnail Image</label>
                <div class="file-upload-area" id="thumbDrop">
                    <input type="file" id="thumbnail" name="thumbnail" class="file-input" accept="image/*">
                    <div class="file-upload-placeholder" id="thumbPlaceholder">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <path d="M21 15l-5-5L5 21" />
                        </svg>
                        <span>Click or drag to upload</span>
                        <small>JPG, PNG, GIF or WebP. Max 2MB.</small>
                    </div>
                    <div class="file-upload-preview hidden" id="thumbPreview">
                        <img src="" alt="Preview">
                        <button type="button" class="file-remove" onclick="removeThumbPreview()">&times;</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="content">Content <span class="text-muted">(Markdown supported)</span></label>
                <div class="editor-toolbar">
                    <button type="button" onclick="insertMd('**','**')" title="Bold"><strong>B</strong></button>
                    <button type="button" onclick="insertMd('*','*')" title="Italic"><em>I</em></button>
                    <button type="button" onclick="insertMd('## ','')" title="Heading 2">H2</button>
                    <button type="button" onclick="insertMd('### ','')" title="Heading 3">H3</button>
                    <div class="toolbar-divider"></div>
                    <button type="button" onclick="insertMd('[','](url)')" title="Link">Link</button>
                    <button type="button" onclick="insertMd('![alt](',')')" title="Image">Img</button>
                    <button type="button" onclick="triggerImageUpload()" title="Upload Image">Upload</button>
                    <div class="toolbar-divider"></div>
                    <button type="button" onclick="insertMd('`','`')" title="Inline Code">Code</button>
                    <button type="button" onclick="insertMd('\n```\n','\n```\n')" title="Code Block">Block</button>
                    <div class="toolbar-divider"></div>
                    <button type="button" onclick="insertMd('- ','')" title="Unordered List">List</button>
                    <button type="button" onclick="insertMd('> ','')" title="Blockquote">Quote</button>
                    <button type="button" onclick="insertMd('\n---\n','')" title="Divider">HR</button>
                </div>
                <textarea id="content" name="content" class="form-textarea editor-textarea" rows="18"
                    placeholder="Start writing your post..." required><?= e(old('content')) ?></textarea>
                <input type="file" id="content-image-upload" class="hidden" accept="image/*" onchange="uploadContentImage(this)">
            </div>

            <div class="form-group">
                <label class="form-label" for="tags">Tags</label>
                <input type="text" id="tags" name="tags" class="form-input"
                    value="<?= e(old('tags')) ?>" placeholder="Separate with commas: php, tutorial, web">
                <small class="form-hint">Create new tags by typing. Existing tags will be reused automatically.</small>
            </div>

            <div class="form-actions">
                <button type="submit" name="submit_action" value="draft" class="btn btn-ghost">Save as Draft</button>
                <button type="submit" name="submit_action" value="published" class="btn btn-primary">Publish Post</button>
            </div>
        </form>
    </div>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>