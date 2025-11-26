import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";
import { PostModel } from "../models/postModel.js";
import { marked } from "marked";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// NOTE - This posts need to be linked to tags, categories
export const PostController = {
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

  // GET /posts/create → show create form
  createForm(req, res) {
    res.render("posts/create");
  },

  // POST /posts → save new post
  async create(req, res, next) {
    try {
      const { title, description, author, read_time, category_id, content } =
        req.body;
      const thumbnail = req.files?.thumbnail;

      // save markdown file
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

      // save thumbnail file if exists
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

      const postId = await PostModel.create({
        title,
        description,
        author,
        read_time,
        category_id,
        post_url: `/uploads/posts/${fileName}`,
        thumbnail_url,
      });

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

      // read markdown content to prefill textarea
      const mdPath = path.join(__dirname, "..", "public", post.post_url);
      let mdContent = "";
      if (fs.existsSync(mdPath)) {
        mdContent = fs.readFileSync(mdPath, "utf-8");
      }

      res.render("posts/edit", { post, mdContent });
    } catch (err) {
      next(err);
    }
  },

  // POST /posts/:id → update post
  async update(req, res, next) {
    try {
      const { title, description, author, read_time, category_id, content } =
        req.body;
      const thumbnail = req.files?.thumbnail;
      const post = await PostModel.getById(req.params.id);

      if (!post) {
        const error = new Error("Post not found");
        error.status = 404;
        return next(error);
      }

      // overwrite markdown file
      const mdPath = path.join(__dirname, "..", "public", post.post_url);
      fs.writeFileSync(mdPath, content, "utf-8");

      // save new thumbnail if uploaded
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

      await PostModel.update(req.params.id, {
        title,
        description,
        author,
        read_time,
        category_id,
        post_url: post.post_url,
        thumbnail_url,
      });

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
