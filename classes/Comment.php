<?php
class Comment
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByPost($postId)
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.name AS author_name, u.profile_image AS author_image
             FROM comments c
             JOIN users u ON c.user_id = u.id
             WHERE c.post_id = ?
             ORDER BY c.created_at DESC"
        );
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    public function create($postId, $userId, $content)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)"
        );
        $stmt->execute([$postId, $userId, $content]);
        return (int) $this->db->lastInsertId();
    }

    public function delete($id)
    {
        $this->db->prepare("DELETE FROM comments WHERE id = ?")->execute([$id]);
    }

    public function getAll($page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT c.*, u.name AS author_name, p.title AS post_title
             FROM comments c
             JOIN users u ON c.user_id = u.id
             JOIN posts p ON c.post_id = p.id
             ORDER BY c.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function getTotal()
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM comments")->fetchColumn();
    }

    public function getRecent($limit = 5)
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.name AS author_name, p.title AS post_title
             FROM comments c
             JOIN users u ON c.user_id = u.id
             JOIN posts p ON c.post_id = p.id
             ORDER BY c.created_at DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
