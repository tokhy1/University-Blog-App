import { TagModel } from "../models/tagModel.js";

export const TagController = {
  // GET /tags → list all tags
  async index(req, res, next) {
    try {
      const tags = await TagModel.getAll();
      res.render("tags/index", { tags });
    } catch (err) {
      next(err);
    }
  },

  // GET /tags/create → show create form
  createForm(req, res) {
    res.render("tags/create");
  },

  // POST /tags → save new tag
  async create(req, res, next) {
    try {
      const { name } = req.body;

      await TagModel.create({
        name
      });

      res.redirect("/tags");
    } catch (err) {
      next(err);
    }
  },

  // GET /tags/:id/edit → show edit form
  async editForm(req, res, next) {
    try {
      const tag = await TagModel.getById(req.params.id);

      if (!tag) {
        const error = new Error("Tag not found");
        error.status = 404;
        return next(error);
      }

      res.render("tags/edit", { tag });
    } catch (err) {
      next(err);
    }
  },

  // POST /tags/:id → update tag
  async update(req, res, next) {
    try {
      const { name } = req.body;

      const success = await TagModel.update(req.params.id, {
        name,
      });

      if (!success) {
        const error = new Error("Tag not found");
        error.status = 404;
        return next(error);
      }

      res.redirect("/tags");
    } catch (err) {
      next(err);
    }
  },

  // POST /tags/:id/delete → delete tag
  async delete(req, res, next) {
    try {
      const success = await TagModel.delete(req.params.id);

      if (!success) {
        const error = new Error("Tag not found");
        error.status = 404;
        return next(error);
      }

      res.redirect("/tags");
    } catch (err) {
      next(err);
    }
  },
};
