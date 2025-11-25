import express from "express";
import { CategoryController } from "../controllers/categoryController.js";

const router = express.Router();

// List all categories
router.get("/", CategoryController.index);

// Create form
router.get("/create", CategoryController.createForm);

// Create category (POST)
router.post("/new", CategoryController.create);

// Edit form
router.get("/:id/edit", CategoryController.editForm);

// Update category (POST)
router.post("/update/:id", CategoryController.update);

// Delete category (POST)
router.post("/:id/delete", CategoryController.delete);

export default router;
