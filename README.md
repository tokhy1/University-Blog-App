# 📝 PHP Blog System

A full-featured Blog Management System built using **PHP & MySQL** that demonstrates real-world backend fundamentals including authentication, authorization, CRUD operations, sessions, cookies, file uploads, and relational database design.

This project was built as a backend-focused academic project and follows clean structure and best practices.

---

# 📌 Project Overview

The Blog System allows users to create and interact with posts while providing an Admin Dashboard with full system control.

The system includes:

- Authentication & Authorization
- Role-based access (Admin / User)
- Post creation & interactions
- File & image uploads
- Admin dashboard for system management

---

# 🎯 Core Features

## 🔐 Authentication System

- User registration
- Login & Logout
- Password hashing & verification
- Session management
- “Remember Me” cookie
- Role-based redirection

### Role Behavior

|Role|Behavior|
|---|---|
|Admin|Redirected to dashboard after login|
|User|Redirected to homepage|
|User accessing dashboard|Automatically blocked & redirected|

---

## 👤 User Features

Users can:

- Create new blog posts
- Edit or delete **their own posts only**
- Comment on any post
- Like posts (one like per post per user)
- Save posts to favorites
- Update profile:
    - Change name
    - Upload profile image
    - Email & password are not editable

---

## 🛠 Admin Features

Admin has full system control via dashboard.

Admin can:

- Manage users (view/delete)
- Manage posts (full CRUD)
- Manage comments (moderation)
- Manage categories (create/edit/delete)
- View system statistics

---

## ✍️ Post System

Each post contains:

- Title
- Thumbnail image
- Markdown content
- Images inside content
- Category
- Tags

### Categories vs Tags

|Feature|Who Creates|
|---|---|
|Categories|Admin only|
|Tags|Users while writing posts|

---

## 💬 Interaction System

Users can interact with posts by:

- Commenting
- Liking
- Saving to favorites

---

# 🗄 Database Design

## Tables Overview

|Table|Purpose|
|---|---|
|users|Store user accounts & roles|
|posts|Blog posts|
|categories|Post categories|
|tags|Post tags|
|post_tags|Many-to-many relationship|
|comments|Post comments|
|likes|Post likes|
|favorites|Saved posts|
|post_images|Images inside posts|

---

## 🔗 Relationships Summary

- User → creates → Posts (1:M)
- Post → belongs to → Category (M:1)
- Post → has → Comments (1:M)
- Post ↔ Tags (M:N)
- User ↔ Likes ↔ Posts (M:N)
- User ↔ Favorites ↔ Posts (M:N)

---

# 🖼 File Upload System

The system supports image uploads in three locations:

|Type|Folder|
|---|---|
|Profile Images|`/uploads/profiles`|
|Post Thumbnails|`/uploads/posts`|
|Post Content Images|`/uploads/post-content`|

Uploads are validated for:

- File type
- File size
- Secure storage of file path in database

---

# 🧰 Technology Stack

### Backend

- PHP (Core PHP)
- MySQL
- PDO (Prepared Statements)
- Sessions & Cookies

### Frontend

- HTML
- CSS
- Vanilla JavaScript

---

# 🔒 Security Practices

The project applies important backend security practices:

- Password hashing using `password_hash()`
- Password verification using `password_verify()`
- Prepared SQL statements (PDO)
- Role-based authorization checks
- Session-based authentication
- Secure file upload validation

---

# 📊 Admin Dashboard (Statistics)

The dashboard includes system insights such as:

- Total users
- Total posts
- Total comments
- Most liked posts

---

# 📁 Project Deliverables

This repository contains:

- Full Source Code
- Database SQL file
- Screenshots of system
- Project report (features & database design)

---

# 🚀 How to Run the Project

1️⃣ Move to the `php-version` branch and Clone the repository

```
git clone <https://github.com/tokhy1/University-Blog-App.git>
```

2️⃣ Move project to your server directory  
Example (XAMPP):

```
htdocs/blog-system
```

3️⃣ Create MySQL database

```
blog_system
```

4️⃣ Import the SQL file

```
database/blog_system.sql
```

5️⃣ Configure database connection  
Edit:

```
config/database.php
```

6️⃣ Start Apache & MySQL

7️⃣ Open in browser

```
http://localhost/blog-system
```

-----------
