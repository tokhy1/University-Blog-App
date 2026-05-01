<?php
class Post
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getPublished($page = 1, $perPage = 9)
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT p.*, c.name AS category_name, u.name AS author_name
             FROM posts p
             JOIN categories c ON p.category_id = c.id
             JOIN users u ON p.user_id = u.id
             WHERE p.status = 'published'
             ORDER BY p.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function getTotalPublished()
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    u.name AS author_name, u.profile_image AS author_image
             FROM posts p
             JOIN categories c ON p.category_id = c.id
             JOIN users u ON p.user_id = u.id
             WHERE p.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByCategory($categoryId, $page = 1, $perPage = 9)
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT p.*, c.name AS category_name, u.name AS author_name
             FROM posts p
             JOIN categories c ON p.category_id = c.id
             JOIN users u ON p.user_id = u.id
             WHERE p.category_id = ? AND p.status = 'published'
             ORDER BY p.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$categoryId, $perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function countByCategory($categoryId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM posts WHERE category_id = ? AND status = 'published'");
        $stmt->execute([$categoryId]);
        return (int) $stmt->fetchColumn();
    }

    public function getByUser($userId)
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.name AS category_name
             FROM posts p
             JOIN categories c ON p.category_id = c.id
             WHERE p.user_id = ?
             ORDER BY p.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create($userId, $categoryId, $title, $content, $thumbnail, $status = 'draft')
    {
        $slug = $this->makeSlug($title);
        $stmt = $this->db->prepare(
            "INSERT INTO posts (user_id, category_id, title, slug, thumbnail, content, status)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $categoryId, $title, $slug, $thumbnail, $content, $status]);
        return (int) $this->db->lastInsertId();
    }

    public function update($id, $categoryId, $title, $content, $thumbnail, $status)
    {
        $slug = $this->makeSlug($title, $id);

        if ($thumbnail !== null) {
            $stmt = $this->db->prepare(
                "UPDATE posts SET category_id=?, title=?, slug=?, thumbnail=?, content=?, status=? WHERE id=?"
            );
            $stmt->execute([$categoryId, $title, $slug, $thumbnail, $content, $status, $id]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE posts SET category_id=?, title=?, slug=?, content=?, status=? WHERE id=?"
            );
            $stmt->execute([$categoryId, $title, $slug, $content, $status, $id]);
        }
    }

    public function updateAuthor($id, $userId)
    {
        $this->db->prepare("UPDATE posts SET user_id = ? WHERE id = ?")->execute([$userId, $id]);
    }

    public function delete($id)
    {
        $post = $this->getById($id);
        if ($post) {
            if ($post['thumbnail'] && file_exists($post['thumbnail'])) {
                unlink($post['thumbnail']);
            }
            $images = $this->db->prepare("SELECT image_path FROM post_images WHERE post_id = ?");
            $images->execute([$id]);
            foreach ($images->fetchAll() as $img) {
                if (file_exists($img['image_path'])) unlink($img['image_path']);
            }
        }
        $this->db->prepare("DELETE FROM posts WHERE id = ?")->execute([$id]);
    }

    public function setTags($postId, $tagIds)
    {
        $this->db->prepare("DELETE FROM post_tags WHERE post_id = ?")->execute([$postId]);
        $stmt = $this->db->prepare("INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)");
        foreach ($tagIds as $tagId) {
            $stmt->execute([$postId, $tagId]);
        }
    }

    public function getTags($postId)
    {
        $stmt = $this->db->prepare(
            "SELECT t.* FROM tags t JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = ?"
        );
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    public function addImage($postId, $imagePath)
    {
        $this->db->prepare("INSERT INTO post_images (post_id, image_path) VALUES (?, ?)")
            ->execute([$postId, $imagePath]);
    }

    public function getAll($page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT p.*, c.name AS category_name, u.name AS author_name
             FROM posts p
             JOIN categories c ON p.category_id = c.id
             JOIN users u ON p.user_id = u.id
             ORDER BY p.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function getTotal()
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    }

    public function getRecent($limit = 5)
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.name AS category_name, u.name AS author_name
             FROM posts p
             JOIN categories c ON p.category_id = c.id
             JOIN users u ON p.user_id = u.id
             ORDER BY p.created_at DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function makeSlug($title, $excludeId = null)
    {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($title)));
        $slug = trim($slug, '-');
        if (!$slug) $slug = 'post';

        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM posts WHERE slug = ?");
            $stmt->execute([$slug]);
        }

        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }
        return $slug;
    }
}
