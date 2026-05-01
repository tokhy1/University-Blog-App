<?php
class Tag
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        return $this->db->query("SELECT * FROM tags ORDER BY name")->fetchAll();
    }

    public function findOrCreate($name)
    {
        $name = trim($name);
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
        $slug = trim($slug, '-');

        $stmt = $this->db->prepare("SELECT id FROM tags WHERE name = ?");
        $stmt->execute([$name]);
        $existing = $stmt->fetch();

        if ($existing) return (int) $existing['id'];

        $this->db->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)")->execute([$name, $slug]);
        return (int) $this->db->lastInsertId();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tags WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function delete($id)
    {
        $this->db->prepare("DELETE FROM tags WHERE id = ?")->execute([$id]);
    }
}
