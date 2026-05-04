# 📌 Blog Website (Frontend + PHP MVC)

## 📖 Project Overview
This project is a Blog Website developed using PHP and MySQL following the MVC (Model-View-Controller) architecture. The system allows users to view blog posts through a clean and user-friendly interface.

The main objective of this project is to demonstrate frontend design along with backend integration using PHP and database connectivity.

---

## 🎯 Features

- 🏠 Home page displaying blog posts
- 📄 Blog detail page for full content
- 📂 Organized MVC structure
- 🗄️ Database integration using MySQL
- 🎨 Responsive UI using HTML & CSS
- 🔐 Session handling (basic)

---

## 🏗️ Project Structure
```
Blog/
│── config/ # Database configuration
│── controller/ # Handles logic and requests
│── model/ # Database operations
│── view/ # UI components
│── pages/ # Page files
│── css/ # Styling
│── database/ # SQL file (blog.sql)
│── index.php # Entry point
│── logout.php # Logout functionality
```

---

## 🛠️ Technologies Used

- HTML
- CSS
- PHP
- MySQL
- XAMPP (Apache Server)

---

## ⚙️ Installation & Setup

### 🔹 Step 1: Clone Repository
```bash
git clone https://github.com/your-username/Blog.git
```

### 🔹 Step 2: Move Project
Move project folder to:
```
C:\xampp\htdocs\
```

### 🔹 Step 3: Start Server
- Open XAMPP
- Start Apache
- Start MySQL

### 🔹 Step 4: Create Database
1. Open phpMyAdmin:
```
http://localhost/phpmyadmin
```
2. Create database:
```
techsync_blog
```

### 🔹 Step 5: Import Database
- Open `techsync_blog`
- Click Import
- Select file:
```database/blog.sql```

### 🔹 Step 6: Run Project
Open browser:
```
http://localhost/Blog/
```

---

## 🧠 MVC Architecture Explanation
- Model → Handles database operations
- View → Displays UI
- Controller → Handles user requests and logic
Flow:
```
User → Controller → Model → Database → View
```

---

## 🚀 Future Enhancements
- User authentication (Login/Signup)
- Admin dashboard
- Add/Edit/Delete blog posts
- Comment system
- Like & Share functionality
