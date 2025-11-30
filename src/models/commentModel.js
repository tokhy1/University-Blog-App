import pool from "./db.js";

export const CommentModel = {
  // Get all comments for a specific post
  async getByPostId(postId) {
    const [rows] = await pool.query(
      `
      SELECT 
        comment_id,
        post_id,
        name,
        email,
        content,
        created_at
      FROM comments
      WHERE post_id = ?
      ORDER BY created_at DESC
      `,
      [postId]
    );
    return rows;
  },

  // Create a new comment
  async create({ post_id, name, email, content }) {
    const [result] = await pool.query(
      `
      INSERT INTO comments (post_id, name, email, content)
      VALUES (?, ?, ?, ?)
      `,
      [post_id, name, email, content]
    );
    return result.insertId;
  },

  // Get comment count for a post
  async getCountByPostId(postId) {
    const [[result]] = await pool.query(
      `
      SELECT COUNT(*) as count
      FROM comments
      WHERE post_id = ?
      `,
      [postId]
    );
    return result.count;
  },

  // Get single comment by ID
  async getById(id) {
    const [[row]] = await pool.query(
      `
      SELECT * FROM comments WHERE comment_id = ?
      `,
      [id]
    );
    return row;
  },
};
