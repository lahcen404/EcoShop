

# 🌱 EcoShop API – Laravel E-commerce Backend

## 📌 Project Overview

EcoShop is a modern **API-first e-commerce platform** dedicated to selling eco-friendly products.

This project is built using **Laravel** as a RESTful backend API that can be consumed by:

* 🌐 Web applications (SPA)
* 📱 Mobile applications
* 🔗 Third-party services

The goal is to design a **scalable, secure, and high-performance API** following Laravel best practices.

---

## 🚀 Features

### 🔐 Authentication (Laravel Sanctum)

* User registration
* User login (token-based authentication)
* User logout
* Retrieve authenticated user profile

---

### 🛍️ User Features

* Browse products
* View product details
* Filter products by category
* Manage shopping cart:

  * Add product to cart
  * Update product quantity
  * Remove product from cart
  * View cart
* Place orders

---

### 📦 Order Processing (Asynchronous)

When an order is placed:

* 📩 A confirmation email is sent
* 📉 Product stock is updated

⚡ These operations are handled asynchronously using:

* Laravel Queues
* Events & Listeners

---

### 🛠️ Admin Features

* Create products
* Update products
* Delete products
* Manage categories
* View orders

---

## ⚙️ Tech Stack

* **Backend:** Laravel (REST API)
* **Authentication:** Laravel Sanctum
* **Database:** MySQL / PostgreSQL
* **Queues:** Redis / Database / Amazon SQS
* **Testing:** Pest
* **API Testing:** Postman

---

## 🏗️ Architecture

This project follows an **API-first architecture**:

* Stateless backend
* JSON-based communication
* No server-side rendering (no Blade)
* Decoupled frontend/backend

---

## 🔄 Workflow (Order Processing)

1. User places an order
2. `OrderPlaced` Event is triggered
3. Listeners dispatch Jobs:

   * Send confirmation email
   * Update product stock
4. Jobs are processed via Queue

---

## 🧪 Testing

Automated tests are implemented using **Pest**.

### ✔️ Covered Scenarios

* User registration
* User authentication
* Access to protected routes
* Product creation (admin)
* Order creation

---

## 🛠️ Installation

```bash
git clone https://github.com/lahcen404/EcoShop.git
cd EcoShop

composer install
cp .env.example .env
php artisan key:generate
```

### Configure Database

Update `.env`:

```
DB_DATABASE=ecoshop
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations:

```bash
php artisan migrate
```

---

## 🔐 Sanctum Setup

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

---

## ⚡ Run Queues

```bash
php artisan queue:work
```

---

## ▶️ Run Server

```bash
php artisan serve
```

---

## 🧪 Run Tests

```bash
php artisan test
```

---

## 🎯 Project Objectives

* Build a clean REST API with Laravel
* Implement secure authentication with Sanctum
* Use Queues for asynchronous processing
* Apply Events & Listeners
* Write automated tests with Pest
* Follow best practices (MVC, clean code)

---

* Postman Collection : https://yguhijopl.postman.co/workspace/Lahcen-Workspace~1eb868fb-6d1e-4a06-bc89-fca36f6d4f58/collection/undefined?action=share&creator=41299916

## 👨‍💻 Author

**Lahcen Ait Maskour**

