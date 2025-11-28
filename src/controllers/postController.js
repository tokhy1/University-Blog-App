import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";
import pool from "../models/db.js";
import { PostModel } from "../models/postModel.js";
import { TagModel } from "../models/tagModel.js";
import { CategoryModel } from "../models/categoryModel.js";
import { marked } from "marked";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export const PostController = {
  // GET / or /posts → Home page with optional filters
  async home(req, res, next) {
    try {
      const { tags, categories } = req.query;
      let posts;

      // Filter by tags if provided
      if (tags) {
        const tagIds = Array.isArray(tags) ? tags : [tags];
        posts = await PostModel.getByTags(tagIds);

        // Add tags to each post
        for (let post of posts) {
          const [postTags] = await pool.query(
            `SELECT tags.tag_id, tags.name FROM tags
             INNER JOIN posts_tags ON tags.tag_id = posts_tags.tag_id
             WHERE posts_tags.post_id = ?`,
            [post.post_id]
          );
          post.tags = postTags;
        }
      }
      // Filter by categories if provided
      else if (categories) {
        const categoryIds = Array.isArray(categories)
          ? categories
          : [categories];
        posts = await PostModel.getByCategories(categoryIds);

        // Add tags to each post
        for (let post of posts) {
          const [postTags] = await pool.query(
            `SELECT tags.tag_id, tags.name FROM tags
             INNER JOIN posts_tags ON tags.tag_id = posts_tags.tag_id
             WHERE posts_tags.post_id = ?`,
            [post.post_id]
          );
          post.tags = postTags;
        }
      }
      // No filters - show all posts
      else {
        posts = await PostModel.getAllWithTags();
      }

      // Use TagModel and CategoryModel instead
      const allTags = await TagModel.getAll();
      const allCategories = await CategoryModel.getAll();

      // Parse selected filters for highlighting
      const selectedTags = tags ? (Array.isArray(tags) ? tags : [tags]) : [];
      const selectedCategories = categories
        ? Array.isArray(categories)
          ? categories
          : [categories]
        : [];

      res.render("home", {
        posts,
        allTags,
        allCategories,
        selectedTags,
        selectedCategories,
      });
    } catch (err) {
      next(err);
    }
  },
  // GET /posts → list all posts
  async index(req, res, next) {
    try {
      const posts = await PostModel.getAll();
      res.render("posts/index", { posts });
    } catch (err) {
      next(err);
    }
  },

  // GET /posts/:id → show single post
  async show(req, res, next) {
    try {
      const post = await PostModel.getById(req.params.id);
      if (!post) {
        const error = new Error("Post not found");
        error.status = 404;
        return next(error);
      }

      // read the Markdown file and convert to HTML
      const mdPath = path.join(__dirname, "..", "public", post.post_url);
      let htmlContent = "";

      if (fs.existsSync(mdPath)) {
        const mdData = fs.readFileSync(mdPath, "utf-8");
        htmlContent = marked(mdData);
      }

      res.render("posts/show", { post, htmlContent });
    } catch (err) {
      next(err);
    }
  },

  // GET /posts/category/:categoryId → show posts by category
  async showByCategory(req, res, next) {
    try {
      const posts = await PostModel.getByCategory(req.params.categoryId);

      // Get category name for display
      const [category] = await pool.query(
        "SELECT name FROM categories WHERE cat_id = ?",
        [req.params.categoryId]
      );

      const categoryName = category[0]?.name || "Unknown Category";

      res.render("posts/index", {
        posts,
        filterType: "category",
        filterName: categoryName,
      });
    } catch (err) {
      next(err);
    }
  },

  // GET /posts/tag/:tagId → show posts by tag
  async showByTag(req, res, next) {
    try {
      const posts = await PostModel.getByTag(req.params.tagId);

      // Get tag name for display
      const [tag] = await pool.query("SELECT name FROM tags WHERE tag_id = ?", [
        req.params.tagId,
      ]);

      const tagName = tag[0]?.name || "Unknown Tag";

      res.render("posts/index", {
        posts,
        filterType: "tag",
        filterName: tagName,
      });
    } catch (err) {
      next(err);
    }
  },

  // GET /posts/create → show create form
  async createForm(req, res, next) {
    try {
      const categories = await CategoryModel.getAll();
      const tags = await TagModel.getAll();
      res.render("posts/create", { categories, tags });
    } catch (err) {
      next(err);
    }
  },

  // POST /posts → save new post
  async create(req, res, next) {
    try {
      const {
        title,
        description,
        author,
        read_time,
        category_id,
        content,
        tags,
      } = req.body;
      const thumbnail = req.files?.thumbnail;

      // Save markdown file
      const fileName = `${Date.now()}-${title.replace(/\s+/g, "-")}.md`;
      const filePath = path.join(
        __dirname,
        "..",
        "public",
        "uploads",
        "posts",
        fileName
      );
      fs.writeFileSync(filePath, content, "utf-8");

      // Save thumbnail file if exists
      let thumbnail_url = null;
      if (thumbnail) {
        const thumbName = `${Date.now()}-${thumbnail.name}`;
        const thumbPath = path.join(
          __dirname,
          "..",
          "public",
          "uploads",
          "images",
          thumbName
        );
        thumbnail.mv(thumbPath);
        thumbnail_url = `/uploads/images/${thumbName}`;
      }

      // Create post
      const postId = await PostModel.create({
        title,
        description,
        author,
        read_time,
        category_id,
        post_url: `/uploads/posts/${fileName}`,
        thumbnail_url,
      });

      // Attach tags if selected
      if (tags) {
        const tagIds = Array.isArray(tags) ? tags : [tags];
        await PostModel.attachTags(postId, tagIds);
      }

      res.redirect(`/posts/${postId}`);
    } catch (err) {
      next(err);
    }
  },

  // GET /posts/:id/edit → show edit form
  async editForm(req, res, next) {
    try {
      const post = await PostModel.getById(req.params.id);
      if (!post) {
        const error = new Error("Post not found");
        error.status = 404;
        return next(error);
      }

      // Read markdown content
      const mdPath = path.join(__dirname, "..", "public", post.post_url);
      let mdContent = "";
      if (fs.existsSync(mdPath)) {
        mdContent = fs.readFileSync(mdPath, "utf-8");
      }

      // Get categories and tags
      const categories = await CategoryModel.getAll();
      const tags = await TagModel.getAll();

      res.render("posts/edit", { post, mdContent, categories, tags });
    } catch (err) {
      next(err);
    }
  },

  // POST /posts/:id → update post
  async update(req, res, next) {
    try {
      const {
        title,
        description,
        author,
        read_time,
        category_id,
        content,
        tags,
      } = req.body;
      const thumbnail = req.files?.thumbnail;
      const post = await PostModel.getById(req.params.id);

      if (!post) {
        const error = new Error("Post not found");
        error.status = 404;
        return next(error);
      }

      // Overwrite markdown file
      const mdPath = path.join(__dirname, "..", "public", post.post_url);
      fs.writeFileSync(mdPath, content, "utf-8");

      // Save new thumbnail if uploaded
      let thumbnail_url = post.thumbnail_url;
      if (thumbnail) {
        const thumbName = `${Date.now()}-${thumbnail.name}`;
        const thumbPath = path.join(
          __dirname,
          "..",
          "public",
          "uploads",
          "images",
          thumbName
        );
        thumbnail.mv(thumbPath);
        thumbnail_url = `/uploads/images/${thumbName}`;
      }

      // Update post
      await PostModel.update(req.params.id, {
        title,
        description,
        author,
        read_time,
        category_id,
        post_url: post.post_url,
        thumbnail_url,
      });

      // Update tags
      await PostModel.clearTags(req.params.id);
      if (tags) {
        const tagIds = Array.isArray(tags) ? tags : [tags];
        await PostModel.attachTags(req.params.id, tagIds);
      }

      res.redirect(`/posts/${req.params.id}`);
    } catch (err) {
      next(err);
    }
  },
  // POST /posts/:id/delete → delete post
  async delete(req, res, next) {
    try {
      const post = await PostModel.getById(req.params.id);
      if (!post) {
        const error = new Error("Post not found");
        error.status = 404;
        return next(error);
      }

      // delete markdown file
      const mdPath = path.join(__dirname, "..", "public", post.post_url);
      if (fs.existsSync(mdPath)) {
        fs.unlinkSync(mdPath);
      }

      await PostModel.delete(req.params.id);
      res.redirect("/posts");
    } catch (err) {
      next(err);
    }
  },
};
