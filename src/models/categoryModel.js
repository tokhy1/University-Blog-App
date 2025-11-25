import pool from "./db.js";

export const CategoryModel = 
  // Get all categories
  async getAll() {
    const [rows] = await pool.query(
      "SELECT * FROM categories ORDER BY cat_id DESC"
    );
    return rows;
  },

  // Get category by ID
  async getById(id) {
    const [rows] = await pool.query(
      "SELECT * FROM categories WHERE cat_id = ? LIMIT 1",
      [id]
    );
    return rows[0];
  },

  // Create new category
  async create({ name, description }) {
    const [result] = await pool.query(
      "INSERT INTO categories (name, description) VALUES (?, ?)",
      [name, description]
    );
    return result.insertId; // returns the new category ID
  },

  // Update category
  async update(id, { name, description }) {
    const [result] = await pool.query(
      "UPDATE categories SET name = ?, description = ? WHERE cat_id = ?",
      [name, description, id]
    );
    return result.affectedRows > 0;
  },

  // Delete category
  async delete(id) {
    const [result] = await pool.query("DELETE FROM categories WHERE cat_id = ?", [
      id,
    ]);
    return result.affectedRows > 0;
  },
};
