import express from "express";
import { PostController } from "../controllers/postController.js";

const router = express.Router();

// GET / → Home page with optional filters
router.get("/", PostController.home);

export default router;
