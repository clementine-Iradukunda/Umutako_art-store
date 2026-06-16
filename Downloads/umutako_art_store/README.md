# 🎨 Umutako Art Store
### A Rwandan Handicraft E-Commerce Web Application
**Course:** EWA408510 – E-Commerce and Web Application | UNILAK 2025-2026

---

## 📋 Project Overview
Umutako Art Store is a full-stack e-commerce platform selling authentic Rwandan handicrafts online. The name *Umutako* means "gift" in Kinyarwanda. Customers can browse products, filter by category, add items to a cart, and place orders — all stored in a MySQL database.

---

## 🛠️ Technologies Used
| Layer      | Technology            |
|------------|-----------------------|
| Frontend   | HTML5, CSS3, JavaScript (Vanilla) |
| Backend    | PHP 8.2               |
| Database   | MySQL 8 (via Workbench or phpMyAdmin) |
| Container  | Docker + Docker Compose |
| CI/CD      | GitHub Actions        |
| Server     | Apache (via XAMPP or Docker) |

---

## 📁 Folder Structure
```
umutako_art_store/
├── index.html              ← Main homepage
├── css/
│   └── style.css           ← All styles (chocolate theme)
├── js/
│   └── main.js             ← Cart, products, checkout logic
├── php/
│   ├── db_connect.php      ← Database connection
│   ├── get_categories.php  ← Returns categories as JSON
│   ├── get_products.php    ← Returns products as JSON
│   └── place_order.php     ← Saves order to database
├── database.sql            ← Run this in MySQL Workbench
├── Dockerfile              ← Docker build file
├── docker-compose.yml      ← Runs PHP + MySQL together
├── .github/
│   └── workflows/
│       └── deploy.yml      ← GitHub Actions CI/CD
└── README.md               ← This file
```

---

## 🚀 Option 1: Run with XAMPP (Recommended for Beginners)

### Step 1 – Install XAMPP
Download from: https://www.apachefriends.org/download.html
Install and open XAMPP Control Panel. Start **Apache** and **MySQL**.

### Step 2 – Set up the database
1. Open **MySQL Workbench** (or go to http://localhost/phpmyadmin)
2. Open the file `database.sql` from this folder
3. Click **Run** (lightning bolt icon in Workbench)
4. You should see the `umutako_art_store` database created with sample data.

### Step 3 – Copy files to XAMPP
1. Copy the entire `umutako_art_store` folder
2. Paste it into: `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)

### Step 4 – Open in browser
Go to: **http://localhost/umutako_art_store/**

---

## 🐳 Option 2: Run with Docker

### Requirements
- Docker Desktop installed: https://www.docker.com/products/docker-desktop/

### Steps
```bash
# 1. Open a terminal in this folder
cd umutako_art_store

# 2. Build and start both services (PHP + MySQL)
docker-compose up --build

# 3. Open in your browser
# http://localhost:8080
```
The database is created automatically from `database.sql` on first run.

---

## 🔧 Configuration
If MySQL asks for a password, edit `php/db_connect.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // ← your username
define('DB_PASS', '');         // ← your password (empty by default in XAMPP)
define('DB_NAME', 'umutako_art_store');
```

---

## ✨ Features
- ✅ Responsive design (mobile-friendly)
- ✅ Product listing with categories
- ✅ Product detail modal popup
- ✅ Search by product name or description
- ✅ Shopping cart (add, remove, update quantity)
- ✅ Cart persists in browser (localStorage)
- ✅ Checkout form with order confirmation
- ✅ Orders saved to MySQL database
- ✅ Docker containerization
- ✅ GitHub Actions CI/CD pipeline

---

## 🎓 Academic Info
- **Student:** Iradukunda Clémentine
- **Course:** EWA408510 – E-Commerce and Web Application
- **University:** UNILAK (University of Lay Adventists of Kigali)
- **Academic Year:** 2025-2026
- **Instructor:** Eric Maniraguha
