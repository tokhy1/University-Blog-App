import express from "express";
import { TagController } from "../controllers/tagController.js";

const router = express.Router();

// List all categories
router.get("/", TagController.index);

// Create form
router.get("/create", TagController.createForm);

// Create category (POST)
router.post("/new", TagController.create);

// Edit form
router.get("/:id/edit", TagController.editForm);

// Update category (POST)
router.post("/update/:id", TagController.update);

// Delete category (POST)
router.post("/:id/delete", TagController.delete);

export default router;
