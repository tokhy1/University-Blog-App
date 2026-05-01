<?php
class Category
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        return $this->db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public function create($name)
    {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($name)));
        $slug = trim($slug, '-');

        $stmt = $this->db->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);
        return (int) $this->db->lastInsertId();
    }

    public function update($id, $name)
    {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($name)));
        $slug = trim($slug, '-');
        $this->db->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?")
            ->execute([$name, $slug, $id]);
    }

    public function delete($id)
    {
        $count = $this->db->prepare("SELECT COUNT(*) FROM posts WHERE category_id = ?");
        $count->execute([$id]);
        if ($count->fetchColumn() > 0) return false;

        $this->db->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        return true;
    }

    public function getPostCount($categoryId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM posts WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        return (int) $stmt->fetchColumn();
    }
}
