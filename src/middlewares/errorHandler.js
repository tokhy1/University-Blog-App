export function errorHandler(err, req, res, next) {
  console.error("Unexpected error:", err);

  // If headers already sent a response, delegate to Express default handler
  if (res.headersSent) {
    return next(err);
  }

  const status = err.status || 500;

  // Send a JSON response containing detailed message to the client
  res.status(status).json({
    success: false,
    message: err.message || "Internal Server Error",
  });
}
