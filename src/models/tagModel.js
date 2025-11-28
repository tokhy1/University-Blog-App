import pool from "./db.js";

export const TagModel = {
  // Get all tags
  async getAll() {
    const [rows] = await pool.query("SELECT * FROM tags ORDER BY tag_id DESC");
    return rows;
  },

  // Get tag by ID
  async getById(id) {
    const [rows] = await pool.query(
      "SELECT * FROM tags WHERE tag_id = ? LIMIT 1",
      [id]
    );
    return rows[0];
  },

  // Create new tag
  async create({ name }) {
    const [result] = await pool.query("INSERT INTO tags (name) VALUES (?)", [
      name,
    ]);
    return result.insertId; // returns the new Tag ID
  },

  // Update tag
  async update(id, { name }) {
    const [result] = await pool.query(
      "UPDATE tags SET name = ? WHERE tag_id = ?",
      [name, id]
    );
    return result.affectedRows > 0;
  },

  // Delete tag
  async delete(id) {
    const [result] = await pool.query("DELETE FROM tags WHERE tag_id = ?", [
      id,
    ]);
    return result.affectedRows > 0;
  },
};
