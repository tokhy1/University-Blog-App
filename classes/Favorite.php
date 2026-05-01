<?php
class Favorite
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function toggle($postId, $userId)
    {
        $stmt = $this->db->prepare("SELECT id FROM favorites WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);

        if ($stmt->fetch()) {
            $this->db->prepare("DELETE FROM favorites WHERE post_id = ? AND user_id = ?")
                ->execute([$postId, $userId]);
            return false;
        }

        $this->db->prepare("INSERT INTO favorites (post_id, user_id) VALUES (?, ?)")
            ->execute([$postId, $userId]);
        return true;
    }

    public function isFavorited($postId, $userId)
    {
        if (!$userId) return false;
        $stmt = $this->db->prepare("SELECT id FROM favorites WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        return (bool) $stmt->fetch();
    }

    public function getByUser($userId)
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.name AS category_name, u.name AS author_name
             FROM favorites f
             JOIN posts p ON f.post_id = p.id
             JOIN categories c ON p.category_id = c.id
             JOIN users u ON p.user_id = u.id
             WHERE f.user_id = ?
             ORDER BY f.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
