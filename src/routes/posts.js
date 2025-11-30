import express from "express";
import { PostController } from "../controllers/postController.js";

const router = express.Router();

// List all posts
router.get("/", PostController.index);

// Show posts by a given category
router.get("/category/:categoryId", PostController.showByCategory);

// Show posts by a given tag
router.get("/tag/:tagId", PostController.showByTag);

// Show form to create a new post
router.get("/create", PostController.createForm);

// Handle creation
router.post("/new", PostController.create);

// Show single post
router.get("/:id", PostController.show);

// Adding a comment for specific post
router.post("/:id/comment", PostController.addComment);

// Show form to edit a post
router.get("/:id/edit", PostController.editForm);

// Handle update
router.post("/update/:id", PostController.update);

// Handle delete
router.post("/:id/delete", PostController.delete);

export default router;
