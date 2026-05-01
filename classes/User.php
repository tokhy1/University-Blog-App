<?php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT id, name, email, role, profile_image, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll($page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, profile_image, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function getTotal()
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    public function updateName($id, $name)
    {
        $this->db->prepare("UPDATE users SET name = ? WHERE id = ?")->execute([$name, $id]);
    }

    public function updateImage($id, $image)
    {
        $this->db->prepare("UPDATE users SET profile_image = ? WHERE id = ?")->execute([$image, $id]);
    }

    public function updateRole($id, $role)
    {
        $this->db->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$role, $id]);
    }

    public function delete($id)
    {
        $user = $this->getById($id);
        if ($user && $user['profile_image'] && file_exists($user['profile_image'])) {
            unlink($user['profile_image']);
        }
        $this->db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'")->execute([$id]);
    }

    public function getPostCount($userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
}
