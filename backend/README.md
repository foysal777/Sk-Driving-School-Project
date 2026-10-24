# DriveMaster Driving School - Backend Documentation

This backend is built with **Pure Core PHP 8+ (No Frameworks)** and **MySQL** database persistence, featuring **OpenAPI 3.0 (Swagger UI)** and an **Admin Dashboard Panel**.

---

## Architecture Overview

```
project-root/
├── admin/                     # Admin Management Panel
│   ├── index.php              # Dashboard with KPI cards & Chart.js analytics
│   ├── login.php              # Secure Authentication
│   ├── logout.php             # Session Termination
│   ├── inquiries.php          # Contact Messages & Leads Manager
│   ├── enrollments.php        # Course Bookings Manager
│   └── courses.php            # Courses & Rates CRUD
│
├── backend/
│   ├── config/
│   │   ├── database.php       # PDO MySQL Configuration & environment variables
│   │   └── app.php            # Global App Settings, Session, and CORS
│   ├── src/
│   │   ├── Database.php       # Singleton PDO Wrapper with prepared statements
│   │   ├── Response.php       # Standardized JSON API response helper
│   │   ├── Auth.php           # Session and Bearer token authentication guard
│   │   └── Models/
│   │       ├── Inquiry.php    # Contact form inquiries model
│   │       ├── Enrollment.php # Course bookings model
│   │       └── Course.php     # Syllabi and pricing packages model
│   ├── api/
│   │   ├── index.php          # Unified RESTful API router
│   │   ├── contact.php        # Direct POST /api/contact endpoint
│   │   ├── enrollments.php    # Direct POST /api/enrollments endpoint
│   │   └── courses.php        # Direct GET /api/courses endpoint
│   ├── swagger/
│   │   ├── index.html         # Interactive Swagger UI
│   │   └── openapi.json       # OpenAPI 3.0 specification
│   └── install.php            # Web-based one-click MySQL schema & seed installer
│
└── database/
    ├── schema.sql             # MySQL table definitions
    └── seed.sql               # Seed data for default admin, courses & inquiries
```

---

## 🚀 Quick Start Guide

### 1. Requirements
- **PHP**: 8.0 or higher (`php-cli`, `php-mysql`, `php-mbstring`)
- **Database**: MySQL 8.0+ or MariaDB 10.4+
- **Web Server**: Built-in PHP server, Apache, Nginx, XAMPP, or Laragon

### 2. Database Setup (Choose One Option)

#### Option A: One-Click Web Installer (Recommended)
1. Start your local server and navigate to:
   ```
   http://localhost:8000/backend/install.php
   ```
2. Enter your MySQL username and password (default `root` / blank).
3. Click **"Run Schema & Seed Database"**. It will automatically create `drivemaster_db`, build tables, and seed initial data.

#### Option B: MySQL CLI / phpMyAdmin
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

### 3. Running with PHP Built-in Server
From the project root directory, run:
```bash
php -S localhost:8000
```

---

## 🔐 Default Admin Credentials

| Role | Username / Email | Password |
|---|---|---|
| Super Administrator | `admin@drivemaster.com` (or `admin`) | `Admin@123456` |

Access the Admin Panel at:
👉 **`http://localhost:8000/admin/`**

---

## 📖 Swagger API Documentation

Access the interactive Swagger UI at:
👉 **`http://localhost:8000/backend/swagger/`**

Features:
- Complete OpenAPI 3.0 schemas for Contact, Courses, Enrollments, and Admin actions.
- Interactive **"Try it out"** testing directly from your browser.
- Bearer token authorization support.

---

## 📡 Key RESTful API Endpoints

| Method | Endpoint | Description | Access |
|---|---|---|---|
| `GET` | `/backend/api/health` | Check API & database connection status | Public |
| `POST` | `/backend/api/contact` | Submit contact email message from form box | Public |
| `GET` | `/backend/api/courses` | List all active driving course packages | Public |
| `POST` | `/backend/api/enrollments` | Submit student lesson booking | Public |
| `POST` | `/backend/api/admin/login` | Authenticate admin user & receive token | Public |
| `GET` | `/backend/api/admin/dashboard` | KPI analytics and lead counts | Protected |
| `GET` | `/backend/api/admin/inquiries` | List student contact inquiries with filters | Protected |
| `PUT` | `/backend/api/admin/inquiries/{id}/status` | Update inquiry status (`new`, `contacted`, `enrolled`) | Protected |
| `DELETE` | `/backend/api/admin/inquiries/{id}` | Delete inquiry | Protected |
| `GET` | `/backend/api/admin/enrollments` | List all course enrollments | Protected |
| `PUT` | `/backend/api/admin/enrollments/{id}/status` | Update booking & payment status | Protected |
| `POST` | `/backend/api/admin/courses` | Create new course package | Protected |
| `PUT` | `/backend/api/admin/courses/{id}` | Update course pricing & syllabus | Protected |
| `DELETE` | `/backend/api/admin/courses/{id}` | Delete course | Protected |
