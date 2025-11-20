# Personal Blog App

A simple personal blog application built with **Node.js, Express.js, EJS, and MySQL**. Users can **create, read, update, and delete posts**, assign them to **categories**, add **tags**, and optionally allow comments. Post thumbnails are supported.  

This project demonstrates **CRUD operations**, **relational database design**, and **server-rendered web applications** with a clean and maintainable code structure.

---

## Features
- Create, view, edit, and delete posts  
- Assign posts to categories  
- Add multiple tags to posts (many-to-many relationship)  
- Optional comments for posts  
- Upload thumbnails for posts  
- Server-rendered with EJS templates  

---

## Tech Stack
- **Backend:** Node.js, Express.js  
- **Template Engine:** EJS  
- **Database:** MySQL  
- **Other Packages:** dotenv, multer, method-override, express-validator  

---

## Installation

1. Clone the repo:

```
git clone https://github.com/your-username/University-Blog-App.git
```
2. Navigate into the project folder:

```
cd University-Blog-App
```

3. Install dependencies:

```
npm install
```

4. Create a `.env` file in the root directory with:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USER=root
DB_PASS=your_mysql_password
DB_NAME=blog_db
PORT=3000
UPLOAD_DIR=public/uploads
```

5. Set up the MySQL database using the provided SQL script.

---

## Running the App

Start the server in development mode:
```
npm run dev
```

Open browser at:
```
http://localhost:3000

```

---

## Folder Structure

```
src/
├─ app.js
├─ routes/
├─ controllers/
├─ models/
├─ views/
└─ public/
```

---





