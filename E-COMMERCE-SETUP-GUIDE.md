# E-Commerce Application - Setup Guide

## ✨ Overview

This is a professional, modern E-commerce application built with **Laravel 11**, **Tailwind CSS**, **Chart.js**, and **Laravel Breeze** authentication. The application features:

- 🔐 Role-Based Access Control (Admin / Customer)
- 📊 Admin Dashboard with Sales Analytics
- 🛍️ Product Catalog Management
- 🛒 Shopping Cart System
- 📦 Order Processing
- 💰 Payment Ready (checkout flow)
- 📈 Monthly Sales Charts
- 🎨 Modern, Minimalist UI Design

---

## 📋 Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL / SQLite
- Git

---

## 🚀 Installation

### 1. Clone & Setup

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 2. Configure Database

Update `.env` file:

```env
DB_CONNECTION=mysql          # or sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run Migrations & Seeding

```bash
# Run migrations
php artisan migrate

# Seed database (creates 1 admin + 5 customers + 100 products + sample orders)
php artisan db:seed
```

### 4. Build Frontend Assets

```bash
npm run build
# or for development with hot reload:
npm run dev
```

### 5. Start Application

```bash
php artisan serve
# Access at: http://localhost:8000
```

---

## 🔑 Default Credentials

### Admin User
- **Email:** admin@example.com
- **Password:** password

### Customer Users
- **Email:** Any of the 5 generated customer emails (e.g., customer1@example.com, etc.)
- **Password:** password

---

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php      # Admin dashboard & analytics
│   │   ├── ProductController.php        # Product CRUD
│   │   ├── CartController.php           # Cart management
│   │   └── OrderController.php          # Order processing
│   ├── Middleware/
│   │   ├── AdminMiddleware.php          # Admin role protection
│   │   └── CustomerMiddleware.php       # Customer role protection
│   ├── Requests/                        # Form validation
│   │   ├── StoreProductRequest.php
│   │   ├── UpdateProductRequest.php
│   │   ├── AddToCartRequest.php
│   │   └── CheckoutRequest.php
│   └── Models/
│       ├── User.php                     # With role support
│       ├── Product.php
│       ├── Cart.php
│       ├── Order.php
│       └── OrderItem.php
│
database/
├── migrations/                          # All database schemas
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 2024_01_01_000010_create_products_table.php
│   ├── 2024_01_01_000011_create_carts_table.php
│   ├── 2024_01_01_000012_create_orders_table.php
│   ├── 2024_01_01_000013_create_order_items_table.php
│   └── ...
├── factories/                           # Model factories for seeding
│   ├── UserFactory.php
│   ├── ProductFactory.php
│   ├── CartFactory.php
│   ├── OrderFactory.php
│   └── OrderItemFactory.php
└── seeders/
    └── DatabaseSeeder.php               # Seeds 100 products & sample data
│
resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php                # Main layout
    │   ├── navigation.blade.php         # Navigation bar
    │   └── footer.blade.php             # Footer
    ├── products/
    │   ├── index.blade.php              # Product catalog
    │   └── show.blade.php               # Product details
    ├── cart/
    │   └── index.blade.php              # Shopping cart
    ├── checkout/
    │   └── index.blade.php              # Checkout page
    ├── orders/
    │   ├── index.blade.php              # Customer's orders list
    │   └── show.blade.php               # Order details
    └── admin/
        ├── dashboard.blade.php          # Admin dashboard with Chart.js
        └── products/
            ├── index.blade.php          # Product management
            ├── create.blade.php         # Create product form
            └── edit.blade.php           # Edit product form

routes/
└── web.php                              # All application routes
```

---

## 🗂️ Database Schema

### Users Table
```sql
- id (Primary Key)
- name (string)
- email (string, unique)
- password (hashed)
- role (enum: 'admin', 'customer')
- timestamps
```

### Products Table
```sql
- id (Primary Key)
- name (string, unique)
- slug (string, indexed)
- description (text)
- price (decimal: 10,2)
- stock (integer, default: 0)
- image_path (string, nullable)
- timestamps
```

### Carts Table
```sql
- id (Primary Key)
- user_id (Foreign Key → users)
- product_id (Foreign Key → products)
- quantity (integer)
- timestamps
- unique constraint: (user_id, product_id)
```

### Orders Table
```sql
- id (Primary Key)
- user_id (Foreign Key → users)
- total_amount (decimal: 10,2)
- status (enum: 'pending', 'processing', 'completed', 'cancelled')
- shipping_address (text)
- timestamps
```

### OrderItems Table
```sql
- id (Primary Key)
- order_id (Foreign Key → orders)
- product_id (Foreign Key → products)
- quantity (integer)
- price (decimal: 10,2)
- timestamps
```

---

## 🔐 Role-Based Access Control (RBAC)

### Admin Routes (`/admin`)
- Protected by `AdminMiddleware`
- Dashboard with sales analytics & charts
- Product CRUD operations
- Order management (mark complete, cancel)
- View all customer orders

### Customer Routes
- Browse products ✓
- Add to cart ✓
- Manage cart items ✓
- Checkout & place orders ✓
- View own orders ✓
- Cannot access `/admin` routes ✗

---

## 🛣️ API Routes Overview

### Public Routes
```
GET    /products              # Product catalog with pagination
GET    /products/{product}    # Product details
```

### Customer Routes (Authenticated)
```
GET    /cart                  # View shopping cart
POST   /cart/add              # Add product to cart
PATCH  /cart/{item}           # Update cart item quantity
DELETE /cart/{item}           # Remove from cart
POST   /cart/clear            # Clear entire cart

GET    /checkout              # Checkout page
POST   /checkout              # Process checkout & create order

GET    /orders                # List customer's orders
GET    /orders/{order}        # View order details
```

### Admin Routes (Authenticated + Admin Role)
```
GET    /admin/dashboard       # Dashboard with analytics
GET    /admin/products        # Manage products list
GET    /admin/products/create # Create product form
POST   /admin/products        # Store product
GET    /admin/products/{id}/edit  # Edit product form
PATCH  /admin/products/{id}   # Update product
DELETE /admin/products/{id}   # Delete product

PATCH  /admin/orders/{order}/complete  # Mark order as complete
PATCH  /admin/orders/{order}/cancel    # Cancel order & restore stock
```

---

## 📊 Admin Dashboard Features

1. **Stats Cards:**
   - Total Revenue (from completed orders)
   - Total Orders Completed
   - Total Products
   - Total Customers

2. **Sales Chart:**
   - Chart.js line chart displaying monthly sales
   - Last 12 months of data
   - Interactive hover tooltips
   - Currency formatting

3. **Recent Orders:**
   - Last 5 completed orders
   - Customer name, amount, date, status
   - Quick action links

4. **Quick Actions:**
   - Manage Products link
   - Add New Product link
   - View Store link

---

## 🎨 UI/UX Design

- **Framework:** Tailwind CSS
- **Style:** Modern, minimalist, clean
- **Color Scheme:** Blue primary (#2563EB), Gray accents
- **Components:**
  - Responsive navigation bar with cart icon
  - Product cards with images, price, stock status
  - Shopping cart with quantity manager
  - Order summary panels
  - Status badges with color coding
  - Toast-style success/error messages

---

## 🔄 Order Processing Flow

1. **Customer selects products** and adds to cart
2. **Navigates to cart** - can update quantities or proceed
3. **Clicks checkout** - redirected to checkout page
4. **Enters shipping address** and submits
5. **Order created** with status "pending" → "processing"
6. **Stock decremented** for each product
7. **Cart cleared** after successful checkout
8. **Customer views order** with items, totals, shipping address
9. **Admin can mark** as "completed" or "cancel" (restores stock)

---

## 🧪 Database Seeding

The `DatabaseSeeder` creates:

✅ **1 Admin User**
- name: "Admin User"
- email: admin@example.com
- role: admin

✅ **5 Customer Users**
- Names: Generated
- Emails: Generated
- role: customer

✅ **100 Products**
- Names, descriptions (generated)
- Prices: $10 - $500 (random)
- Stock: 5 - 100 units (random)
- Images: Placeholder image paths

✅ **Sample Order History**
- Each customer has 2-4 completed orders
- Each order has 2-5 items
- Order totals calculated from items

### Seed Command:
```bash
php artisan db:seed
```

---

## 🛠️ Key Features

### Product Management
- Full CRUD with validation
- Stock tracking
- Slug generation for URLs
- Batch operations support

### Cart System
- Database-backed (persistent)
- Update quantities on-the-fly
- Clear cart in one click
- Real-time total calculation

### Order Management
- Complete order history
- Status tracking (pending → processing → completed)
- Stock integration (decrements on order, restores on cancel)
- Shipping address storage

### Security
- Middleware-based access control
- Form Request validation (custom rules)
- CSRF protection
- Data authorization checks
- Safe stock operations (transactions)

---

## 📦 Dependencies

Key packages included:
- laravel/framework 11
- laravel/breeze (auth scaffolding)
- tailwindcss (CSS framework)
- chart.js (analytics charts)

---

## 🚨 Important Notes

1. **Authentication:** Uses Laravel Breeze with email/password
2. **Session Management:** Database-backed sessions
3. **Validation:** Custom Form Requests for all inputs
4. **Transactions:** Order checkout wrapped in DB transaction
5. **Stock Management:** Decremented on order placement, restored on cancellation

---

## 📝 Customization

### Add Payment Gateway
Update `OrderController@checkout()` to integrate Stripe/PayPal before marking order as "completed"

### Customize Email Notifications
Create mailable classes and send on order placement/confirmation

### Add Product Filters
Update `ProductController@index()` to support category/price filtering

### Implement Reviews
Create Review model and add to product show page

---

## 🐛 Troubleshooting

**Issue: Assets not loading**
```bash
npm run build
php artisan cache:clear
```

**Issue: Database migration errors**
```bash
php artisan migrate:fresh  # WARNING: Clears all data
php artisan db:seed
```

**Issue: Middleware errors**
- Ensure bootstrap/app.php middleware aliases are registered
- Check that auth guards are properly configured

---

## 📞 Support

For issues or questions, review the Laravel documentation:
- [Laravel Documentation](https://laravel.com)
- [Tailwind CSS Docs](https://tailwindcss.com)
- [Chart.js Docs](https://www.chartjs.org)

---

## 📄 License

This project is open-source and available under the MIT License.

---

**Last Updated:** 2024-01-01  
**Version:** 1.0.0
