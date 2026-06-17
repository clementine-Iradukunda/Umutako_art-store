# 🎨 Umutako Art Store

> **Live Site:** [http://umutakoartstore.xo.je](http://umutakoartstore.xo.je/?i=1)  
> **Repository:** [github.com/clementine-Iradukunda/Umutako_art-store](https://github.com/clementine-Iradukunda/Umutako_art-store)

A full-stack e-commerce web application for authentic Rwandan handicrafts — baskets, ceramics, wood carvings, jewelry, and textiles. Built with HTML, CSS, vanilla JavaScript, PHP, and MySQL.

---

## 📸 Preview

| Home / Shop | Product Detail | Admin Dashboard |
|---|---|---|
| Browse 15+ handcrafted products | Full product detail with related items | Stats, orders, products, customers |

---

## ✨ Features

### 🛍️ Customer Storefront
- Responsive product grid with category filter and live search
- Product detail page with related products
- Shopping cart persisted in `localStorage`
- Checkout form — saves orders to the database
- Toast notifications and smooth animations

### 👤 Customer Account
- Register / Login with email and password
- View personal order history
- View individual order detail with status tracking

### 🔐 Admin Panel
- **Dashboard** — total orders, revenue, pending count, product count, customer count
- **Orders** — list all orders, filter by status, view full detail, update order status
- **Products** — add, edit, delete products with stock management
- **Categories** — add, edit, delete product categories with emoji icons
- **Customers** — list all registered customers with order count and total spend

---

## 🗂️ Project Structure

```
umutako_art_store/
│
├── index.html              # Main storefront
├── product.html            # Product detail page
├── login.html              # Login & Register page
├── my-orders.php           # Customer order history (protected)
├── order-detail.php        # Customer order detail (protected)
│
├── admin/
│   ├── dashboard.php       # Admin dashboard
│   ├── orders.php          # Manage orders
│   ├── products.php        # Manage products
│   ├── categories.php      # Manage categories
│   ├── customers.php       # View customers
│   ├── _header.php         # Shared admin nav/header
│   └── _footer.php         # Shared admin footer
│
├── php/
│   ├── db_connect.php      # Database connection
│   ├── auth_session.php    # Session helpers (requireLogin, requireAdmin)
│   ├── login.php           # Login API (POST)
│   ├── register.php        # Register API (POST)
│   ├── logout.php          # Destroys session, redirects
│   ├── session_info.php    # Returns session JSON for navbar
│   ├── get_products.php    # Products API (GET, supports ?category & ?search)
│   ├── get_product.php     # Single product API (GET ?id=)
│   ├── get_categories.php  # Categories API (GET)
│   └── place_order.php     # Place order API (POST)
│
├── js/
│   ├── main.js             # Cart, products, checkout, categories, search
│   └── product.js          # Product detail page logic
│
├── css/
│   ├── style.css           # Main theme (chocolate & gold)
│   └── admin.css           # Admin panel styles
│
├── images/
│   └── logo.png
│
├── database.sql            # Original local DB setup script
├── docker-compose.yml      # Docker setup
├── Dockerfile
└── .github/
    └── workflows/
        └── deploy.yml      # CI/CD workflow
```

---

## 🗄️ Database Schema

```sql
categories    -- id, name, description, icon, created_at
users         -- id, name, email, password (SHA256), role, phone, address, created_at
products      -- id, name, description, price, stock, category_id, image, created_at
customers     -- id, name, email, phone, address, created_at
orders        -- id, user_id, customer_name, email, phone, address, total, status, notes, created_at
order_items   -- id, order_id, product_id, product_name, quantity, unit_price
```

**Order status flow:** `pending` → `confirmed` → `shipped` → `delivered` (or `cancelled`)

---

## 🔑 Default Credentials

| Role     | Email                    | Password       |
|----------|--------------------------|----------------|
| Admin    | admin@umutako.rw         | Admin@1234     |
| Customer | customer@umutako.rw      | Customer@1234  |

> ⚠️ Change these passwords immediately after deploying to production.

---

## 🚀 Local Setup (XAMPP)

### Requirements
- XAMPP (Apache + MySQL) or any PHP 7.4+ / MySQL 5.7+ stack

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/clementine-Iradukunda/Umutako_art-store.git
   cd Umutako_art-store
   ```

2. **Move to XAMPP htdocs**
   ```bash
   # Windows
   move Umutako_art-store C:\xampp\htdocs\umutako_art_store
   ```

3. **Create the database**
   ```bash
   # In XAMPP MySQL CLI
   mysql -u root < database.sql
   ```

4. **Configure database connection**  
   Edit `php/db_connect.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'umutako_art_store');
   ```

5. **Start Apache & MySQL** in XAMPP Control Panel

6. **Open in browser**
   ```
   http://localhost/umutako_art_store/
   ```
   > If Apache is on port 8080: `http://localhost:8080/umutako_art_store/`

---

## 🐳 Docker Setup

```bash
docker-compose up --build
```
Then open `http://localhost:8080`

---

## 🌐 Live Deployment

The site is hosted on **InfinityFree**:

| Setting       | Value                                  |
|---------------|----------------------------------------|
| Host          | sql212.infinityfree.com                |
| Database      | if0_42203229_umutako_art_store         |
| PHP Version   | 7.4+                                   |
| Live URL      | http://umutakoartstore.xo.je           |

### Deploy Steps
1. Upload all files via FTP or File Manager to `htdocs/`
2. Import `umutako_live_export.sql` via phpMyAdmin
3. Ensure `php/db_connect.php` has live server credentials

---

## 🛠️ Tech Stack

| Layer      | Technology                        |
|------------|-----------------------------------|
| Frontend   | HTML5, CSS3, Vanilla JavaScript   |
| Backend    | PHP 7.4+                          |
| Database   | MySQL / MariaDB                   |
| Fonts      | Google Fonts (Playfair Display, Lato) |
| Auth       | PHP Sessions + SHA256 passwords   |
| Hosting    | InfinityFree                      |
| Version Control | Git / GitHub                 |

---

## 📱 Responsive Design

- Mobile-first responsive layout
- Hamburger navigation on mobile
- Cart sidebar slides in on all screen sizes
- Admin panel adapts to tablet/desktop

---

## 🔒 Security

- Passwords hashed with SHA-256
- All SQL queries use prepared statements (no SQL injection)
- Session-based authentication with role checks
- Admin routes protected by `requireAdmin()` — redirect to login if unauthenticated
- Customer routes protected by `requireCustomer()`

---

## 👩‍💻 Author

**Clementine Iradukunda**  
📧 clementineiradukunda65@gmail.com  
🐙 [github.com/clementine-Iradukunda](https://github.com/clementine-Iradukunda)

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

*Handcrafted in Rwanda. Built with ❤️*
