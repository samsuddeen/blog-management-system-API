# Blog Management System (API)

A Laravel 10-based API for managing blog posts, categories, and users with token-based authentication, role-based permissions, and Excel import/export functionality. Built to assess real-world Laravel development skills as per the provided task requirements.

## Features

✅ **User Authentication & Role Permissions**
   - Token-based authentication using Laravel Sanctum.
   - Role and permission management using Spatie Laravel Permission.
   - Roles: `admin`, `editor`.
   - Permissions: `post-create`, `post-edit`, `post-delete`, `category-manage`, `user-manage`.
   - Restricted access to routes based on permissions.

✅ **Post Management**
   - Authenticated users with appropriate permissions can create, update, and delete posts.
   - Post fields: `title`, `body`, `category_id`, `author_id`.
   - Only the post author or admin can edit/delete a post.

✅ **Category Management**
   - Admins can create, list, and delete categories.
   - Prevents deletion of categories with associated posts.
   - Supports Excel import/export for categories.
   - Soft delete with force delete and restore functionality.

✅ **Public Post Listing**
   - Public endpoint (`/api/public/posts`) to list all posts without authentication.
   - Includes: `title`, `author_name`, `category_name`, `created_at`.

✅ **Excel Import/Export**
   - Export all categories and posts to Excel.
   - Import categories from Excel with columns: `name`, `slug`.

✅ **Bonus Features**
   - FormRequest for validation.
   - API Resources for response formatting.
   - Unit tests for key routes (in `tests/Feature/PostTest.php`).
   - Large data export support (tested with 1000+ categories).

## System Requirements

PHP 8.1 or later

Composer

MySQL (Database: blogs_management)

Node.js (for frontend, if applicable)

Installation Guide

Follow these steps to set up and run the project:

1️⃣ Clone the Repository

git clone https://github.com/samsuddeen/blog-management-system-API.git
cd blog-management-system-API

2️⃣ Install Dependencies

composer install
npm install

3️⃣ Configure Environment

Rename .env_backup to .env and update database credentials:

or 

cp .env_backup .env

Update database settings in .env file:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blogs_management_api
DB_USERNAME=root
DB_PASSWORD=''

4️⃣ Import Database

The database dump file is provided in the repository (blogs_management_api.sql). Import it using:

mysql -u your_username -p blogs_management < blogs_management_api.sql

OR import manually using phpMyAdmin.

5️⃣ Run Migrations & Seed Data

php artisan migrate --seed

This will create tables and insert default admin and user credentials.

6️⃣ Create Storage Link

Since the project uses Laravel File Manager, run:

php artisan storage:link

Also, update your .env file:

APP_URL=http://127.0.0.1:8000

7️⃣ Run the Application

php artisan serve

Now, visit http://127.0.0.1:8000 in your browser.

Admin & User Login Credentials

Admin Login

Email: admin@gmail.com
Password: admin123


API Endpoints
I already Pushed Postman Collection named "Blog Management API.postman_collection.json"

Method

Endpoint

method

Route

Auth

Description

POST

/api/register

❌

Register user

POST

/api/login

❌

Login and get token

GET

/api/posts

✅

List all posts

POST

/api/posts

✅

Create new post

PUT

/api/posts/{id}

✅

Update post

DELETE

/api/posts/{id}

✅

Delete post

GET

/api/categories

✅

List all categories

POST

/api/categories

✅

Add a category

DELETE

/api/categories/{id}

✅

Delete a category

POST

/api/categories/import

✅

Import from Excel

GET

/api/categories/export

✅

Export to Excel

GET

/api/public/posts

❌

List all posts (public)


