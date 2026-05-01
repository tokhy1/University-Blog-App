<?php
class Like
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function toggle($postId, $userId)
    {
        $stmt = $this->db->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);

        if ($stmt->fetch()) {
            $this->db->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?")
                ->execute([$postId, $userId]);
            return false;
        }

        $this->db->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)")
            ->execute([$postId, $userId]);
        return true;
    }

    public function isLiked($postId, $userId)
    {
        if (!$userId) return false;
        $stmt = $this->db->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        return (bool) $stmt->fetch();
    }

    public function getCount($postId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
        $stmt->execute([$postId]);
        return (int) $stmt->fetchColumn();
    }
}
