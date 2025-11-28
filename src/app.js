import "dotenv/config";
import express from "express";
import path from "path";
import { fileURLToPath } from "url";
import methodOverride from "method-override";
import fileUpload from "express-fileupload";

// import DB to initialize pool/test connection
import "./models/db.js";

// routes
import indexRoutes from "./routes/index.js";
import categoryRoutes from "./routes/categories.js";
import postRoutes from "./routes/posts.js";
import tagRoutes from "./routes/tags.js";

// error handler
import { errorHandler } from "./middlewares/errorHandler.js";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();

// -------------------
// VIEW ENGINE
// -------------------
app.set("views", path.join(__dirname, "views"));
app.set("view engine", "ejs");

// -------------------
// STATIC + BODY PARSING
// -------------------
app.use(express.static(path.join(__dirname, "public")));
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(methodOverride("_method"));

// -------------------
// FILE UPLOAD
// -------------------
app.use(
  fileUpload({
    createParentPath: true,
    limits: { fileSize: 10 * 1024 * 1024 },
    abortOnLimit: true,
  })
);

// -------------------
// ROUTES
// -------------------
app.use("/", indexRoutes); // ADD THIS - Home page route
app.use("/categories", categoryRoutes);
app.use("/posts", postRoutes);
app.use("/tags", tagRoutes);

// -------------------
// ERROR HANDLER
// -------------------
app.use(errorHandler);

// -------------------
// START SERVER
// -------------------
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});
