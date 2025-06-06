Blog Management System (Laravel 10)

This is a simple Blog Management System built using Laravel 10. It allows admins to manage blog posts and provides API endpoints for frontend consumption.

Features

✅ User Authentication (Admin & User roles)✅ Blog CRUD Operations (Create, Read, Update, Delete)✅ Blog Categories Management✅ REST API for blogs with authentication using Laravel Sanctum✅ File Uploads using Laravel File Manager

System Requirements

PHP 8.1 or later

Composer

MySQL (Database: blogs_management)

Node.js (for frontend, if applicable)

Installation Guide

Follow these steps to set up and run the project:

1️⃣ Clone the Repository

git clone https://github.com/samsuddeen/blog-management-system-API.git
cd Blog_Management

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
DB_DATABASE=blogs_management
DB_USERNAME=your_username
DB_PASSWORD=your_password

4️⃣ Import Database

The database dump file is provided in the repository (blogs_management.sql). Import it using:

mysql -u your_username -p blogs_management < blogs_management.sql

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


