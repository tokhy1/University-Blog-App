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
};
