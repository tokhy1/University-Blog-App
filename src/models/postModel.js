import pool from "./db.js";

export const PostModel = {
  // Get all posts with category name
  async getAll() {
    const [rows] = await pool.query(
      `
      SELECT
        posts.post_id,
        posts.title,
        posts.description,
        posts.author,
        posts.read_time,
        posts.created_at,
        posts.post_url,
        posts.thumbnail_url,
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

  // Get posts by category
  async getByCategory(categoryId) {
    const [rows] = await pool.query(
      `
    SELECT
      posts.post_id,
      posts.title,
      posts.description,
      posts.author,
      posts.read_time,
      posts.created_at,
      posts.post_url,
      posts.thumbnail_url,
      categories.name AS category_name
    FROM posts
    LEFT JOIN categories ON posts.category_id = categories.cat_id
    WHERE posts.category_id = ?
    ORDER BY posts.post_id DESC
    `,
      [categoryId]
    );
    return rows;
  },

  // Get posts by tag
  async getByTag(tagId) {
    const [rows] = await pool.query(
      `
    SELECT
      posts.post_id,
      posts.title,
      posts.description,
      posts.author,
      posts.read_time,
      posts.created_at,
      posts.post_url,
      posts.thumbnail_url,
      categories.name AS category_name
    FROM posts
    LEFT JOIN categories ON posts.category_id = categories.cat_id
    INNER JOIN posts_tags ON posts.post_id = posts_tags.post_id
    WHERE posts_tags.tag_id = ?
    ORDER BY posts.post_id DESC
    `,
      [tagId]
    );
    return rows;
  },

  // Get posts by multiple tags (any post that has at least one of the tags)
  async getByTags(tagIds) {
    if (!tagIds || tagIds.length === 0) return [];

    const placeholders = tagIds.map(() => "?").join(",");
    const [rows] = await pool.query(
      `
      SELECT DISTINCT
        posts.post_id,
        posts.title,
        posts.description,
        posts.author,
        posts.read_time,
        posts.created_at,
        posts.post_url,
        posts.thumbnail_url,
        categories.name AS category_name,
        posts.category_id
      FROM posts
      LEFT JOIN categories ON posts.category_id = categories.cat_id
      INNER JOIN posts_tags ON posts.post_id = posts_tags.post_id
      WHERE posts_tags.tag_id IN (${placeholders})
      ORDER BY posts.post_id DESC
      `,
      tagIds
    );
    return rows;
  },

  // Get posts by multiple categories
  async getByCategories(categoryIds) {
    if (!categoryIds || categoryIds.length === 0) return [];

    const placeholders = categoryIds.map(() => "?").join(",");
    const [rows] = await pool.query(
      `
      SELECT
        posts.post_id,
        posts.title,
        posts.description,
        posts.author,
        posts.read_time,
        posts.created_at,
        posts.post_url,
        posts.thumbnail_url,
        categories.name AS category_name,
        posts.category_id
      FROM posts
      LEFT JOIN categories ON posts.category_id = categories.cat_id
      WHERE posts.category_id IN (${placeholders})
      ORDER BY posts.post_id DESC
      `,
      categoryIds
    );
    return rows;
  },

  // Get posts with their tags (for displaying tags on each post card)
  async getAllWithTags() {
    const posts = await this.getAll();

    // Fetch tags for all posts
    for (let post of posts) {
      const [tags] = await pool.query(
        `
        SELECT tags.tag_id, tags.name
        FROM tags
        INNER JOIN posts_tags ON tags.tag_id = posts_tags.tag_id
        WHERE posts_tags.post_id = ?
        `,
        [post.post_id]
      );
      post.tags = tags;
    }

    return posts;
  },

  // Create post
  async create({
    title,
    description,
    author,
    read_time,
    category_id,
    post_url,
    thumbnail_url,
  }) {
    const [result] = await pool.query(
      `
      INSERT INTO posts (title, description, author, read_time, category_id, post_url, thumbnail_url)
      VALUES (?, ?, ?, ?, ?, ?, ?)
      `,
      [
        title,
        description,
        author,
        read_time,
        category_id,
        post_url,
        thumbnail_url,
      ]
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
  async update(id, { title, description }) {
    const [result] = await pool.query(
      `
      UPDATE posts
      SET title = ?, description = ?
      WHERE post_id = ?
      `,
      [title, description, id]
    );

    return result.affectedRows > 0;
  },

  // Delete post
  async delete(id) {
    // delete tags first
    await pool.query(`DELETE FROM posts_tags WHERE post_id = ?`, [id]);

    // delete post
    const [result] = await pool.query(`DELETE FROM posts WHERE post_id = ?`, [
      id,
    ]);

    return result.affectedRows > 0;
  },
};
