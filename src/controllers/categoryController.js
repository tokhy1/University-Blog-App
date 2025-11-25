import { CategoryModel } from "../models/categoryModel.js";

export const CategoryController = {
  // GET /categories → list all categories
  async index(req, res, next) {
    try {
      const categories = await CategoryModel.getAll();
      res.render("categories/index", { categories });
    } catch (err) {
      next(err);
    }
  },

  // GET /categories/create → show create form
  createForm(req, res) {
    res.render("categories/create");
  },

  // POST /categories → save new category
  async create(req, res, next) {
    try {
      const { name, description } = req.body;

      await CategoryModel.create({
        name,
        description: description || null,
      });

      res.redirect("/categories");
    } catch (err) {
      next(err);
    }
  },

  // GET /categories/:id/edit → show edit form
  async editForm(req, res, next) {
    try {
      const category = await CategoryModel.getById(req.params.id);

      if (!category) {
        const error = new Error("Category not found");
        error.status = 404;
        return next(error);
      }

      res.render("categories/edit", { category });
    } catch (err) {
      next(err);
    }
  },

  // POST /categories/:id → update category
  async update(req, res, next) {
    try {
      const { name, description } = req.body;

      const success = await CategoryModel.update(req.params.id, {
        name,
        description: description || null,
      });

      if (!success) {
        const error = new Error("Category not found");
        error.status = 404;
        return next(error);
      }

      res.redirect("/categories");
    } catch (err) {
      next(err);
    }
  },

  // POST /categories/:id/delete → delete category
  async delete(req, res, next) {
    try {
      const success = await CategoryModel.delete(req.params.id);

      if (!success) {
        const error = new Error("Category not found");
        error.status = 404;
        return next(error);
      }

      res.redirect("/categories");
    } catch (err) {
      next(err);
    }
  },
};
