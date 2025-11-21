import pool from "./db.js";

export const PostModel = {
  // Get all posts with category name
  async getAll() {
    const [rows] = await pool.query(
      `
      SELECT
        posts.post_id,
        posts.title,
        posts.content,
        posts.thumbnail_url,
        posts.created_at,
        categories.name AS category_name
      FROM posts
      LEFT JOIN categories ON posts.category_id = categories.cat_id
      ORDER BY posts.post_id DESC
      `
    );
    return rows;
  },

  // Get single post with its tags
  async getById(id) {
    const [[post]] = await pool.query(
      `
      SELECT
        posts.*,
        categories.name AS category_name
      FROM posts
      LEFT JOIN categories ON posts.category_id = categories.cat_id
      WHERE posts.post_id = ?
      `,
      [id]
    );

    if (!post) return null;

    // fetch tags
    const [tags] = await pool.query(
      `
      SELECT tags.tag_id, tags.name
      FROM tags
      INNER JOIN posts_tags ON tags.tag_id = posts_tags.tag_id
      WHERE posts_tags.post_id = ?
      `,
      [id]
    );

    post.tags = tags;
    return post;
  },

  // Create post
  async create({ title, content, category_id, thumbnail }) {
    const [result] = await pool.query(
      `
      INSERT INTO posts (title, content, category_id, thumbnail_url)
      VALUES (?, ?, ?, ?)
      `,
      [title, content, category_id, thumbnail]
    );

    return result.insertId;
  },

  // Attach tags to post
  async attachTags(postId, tagIds) {
    if (!tagIds || tagIds.length === 0) return;

    const values = tagIds.map((tagId) => [postId, tagId]);

    await pool.query(
      `
      INSERT INTO posts_tags (post_id, tag_id)
      VALUES ?
      `,
      [values]
    );
  },

  // Clear tags before updating
  async clearTags(postId) {
    await pool.query(`DELETE FROM posts_tags WHERE post_id = ?`, [postId]);
  },

  // Update post
  async update(id, { title, content, category_id, thumbnail }) {
    const [result] = await pool.query(
      `
      UPDATE posts
      SET title = ?, content = ?, category_id = ?, thumbnail_url = ?
      WHERE post_id = ?
      `,
      [title, content, category_id, thumbnail, id]
    );

    return result.affectedRows > 0;
  },

  // Delete post
  async delete(id) {
    // delete tags first
    await pool.query(`DELETE FROM posts_tags WHERE post_id = ?`, [id]);

    // delete post
    const [result] = await pool.query(`DELETE FROM posts WHERE post_id = ?`, [id]);

    return result.affectedRows > 0;
  },
};
