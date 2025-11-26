// src/app.js
import "dotenv/config";
import express from "express";
import path from "path";
import { fileURLToPath } from "url";
import methodOverride from "method-override";
import fileUpload from "express-fileupload";

// import DB to initialize pool/test connection
import "./models/db.js";

// routes
import categoryRoutes from "./routes/categories.js";
import postRoutes from "./routes/posts.js";

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
app.use(express.static(path.join(__dirname, "public"))); // serves /public
app.use(express.urlencoded({ extended: true })); // parse urlencoded form data
app.use(express.json()); // parse JSON
app.use(methodOverride("_method")); // for PUT/DELETE forms

// -------------------
// FILE UPLOAD
// -------------------
app.use(
  fileUpload({
    createParentPath: true, // automatically create folders if missing
    limits: { fileSize: 10 * 1024 * 1024 }, // max 10MB
    abortOnLimit: true,
  })
);

// -------------------
// ROUTES
// -------------------
app.use("/categories", categoryRoutes);
app.use("/posts", postRoutes);

// root redirect
app.get("/", (req, res) => res.redirect("/posts"));

// -------------------
// ERROR HANDLER (LAST)
// -------------------
app.use(errorHandler);

// -------------------
// START SERVER
// -------------------
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});
