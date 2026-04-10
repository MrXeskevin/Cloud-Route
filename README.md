<<<<<<< HEAD
# Cloud Route - Intelligent Transportation System

<div align="center">

![Cloud Route](https://img.shields.io/badge/Cloud-Route-blue?style=for-the-badge)
=======
#  Coud Route -  Transportation Management System

<div align="center">

![Cloud Route](https://img.shields.io/badge/Campus-Connect-blue?style=for-the-badge)
>>>>>>> 704b0f5495e41cfba1633be8bb8c53e201989672
![Version](https://img.shields.io/badge/version-2.0-green?style=for-the-badge)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-OOP-blue?style=for-the-badge)

**A modern, professional web application for managing fleet and passenger transportation**

<<<<<<< HEAD
Built for **modern transit organizations**
=======
Built by  **Group10 BCS CloudComputing**
>>>>>>> 704b0f5495e41cfba1633be8bb8c53e201989672

[Features](#-features) • [Installation](#-installation) • [Usage](#-usage) • [Tech Stack](#-tech-stack) • [Documentation](#-documentation)

</div>

---

##  Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Installation](#-installation)
- [Database Setup](#-database-setup)
- [Usage](#-usage)
- [Project Structure](#-project-structure)
- [API Endpoints](#-api-endpoints)
- [Screenshots](#-screenshots)
- [Security Features](#-security-features)
- [Contributing](#-contributing)
- [Credits](#-credits)

---

##  Overview

<<<<<<< HEAD
**Cloud Route** is a professional, feature-rich web-based transportation management system designed to optimize routes, manage bookings, and provide real-time tracking for modern transit organizations. It provides:
=======
**Cloud Route** is a comprehensive transportation management system designed specifically for university students. It provides:
>>>>>>> 704b0f5495e41cfba1633be8bb8c53e201989672

 **Real-time vehicle tracking** with interactive maps  
 **Online seat booking** with seat selection  
 **Schedule management** for all routes  
 **Issue reporting** system  
 **Admin dashboard** for system management  
 **PWA support** - Install as mobile app  

---

## Features

### 🚀 Features
- **Live Route Tracking**: Monitor vehicles in real-time on interactive maps.
- **Instant Booking**: Secure seats and manage reservations effortlessly.
- **Smart Scheduling**: View up-to-date arrival and departure times.
- **Admin Control**: Robust management of fleets, drivers, and reports.
- **Issue Reporting**: Direct channel for updates and service feedback.
- **Notifications**: Get updates about your bookings.
- **Mobile Responsive**: Works perfectly on all devices.

### For Administrators
- **User Management**: Manage member accounts.
- **Fleet Management**: Add, edit, and monitor vehicles.
- **Driver Management**: Assign and manage drivers.
- **Analytics Dashboard**: View system statistics.
- **Report Management**: Handle user feedback and issues.
- **Booking Overview**: Monitor all active reservations.

---

##  Tech Stack

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with gradients and animations
- **Bootstrap 5.3** - Responsive UI framework
- **JavaScript (ES6+)** - Interactive functionality
- **Font Awesome 6** - Professional icon library
- **Leaflet.js** - Interactive maps

### Backend
- **PHP 7.4+** - Object-Oriented Programming (OOP)
- **MySQL** - Relational database with constraints
- **AJAX/Fetch API** - Asynchronous communication

### Additional
- **PWA** - Progressive Web App capabilities
- **Service Worker** - Offline functionality
- **JSON** - Data exchange format

---

##  Installation

### Prerequisites
- **XAMPP** (or WAMP/LAMP/MAMP)
  - Apache 2.4+
  - MySQL 5.7+ / MariaDB 10.3+
  - PHP 7.4+
- Modern web browser (Chrome, Firefox, Edge, Safari)
- Internet connection (for CDN resources)

### Step-by-Step Guide

#### 1. Download/Clone the Project
```bash
# Clone from repository (if using Git)
git clone https://github.com/your-repo/cloud-route.git

# Or download and extract to your web server directory:
/path/to/server/Cloud_Route
```

#### 2. Start XAMPP Services
1. Open **XAMPP Control Panel**
2. Start **Apache** (Port 80)
3. Start **MySQL** (Port 3306)

#### 3. Import Database
**Method 1: Using phpMyAdmin**
1. Open browser: `http://localhost/phpmyadmin`
2. Click "New" → Database name: `cloud_route`
3. Collation: `utf8mb4_unicode_ci` → Click "Create"
4. Click on `cloud_route` database
5. Click "Import" tab
6. Choose file: `sql/cloud_route.sql`
7. Click "Go" at bottom

#### 4. Configure Database Connection
Open `backend/classes/Database.php` and verify:
```php
private $host = 'localhost';
private $username = 'root';
private $password = '';  // Empty for default XAMPP
private $database = 'cloud_route';
```

#### 5. Access the Application
Open your browser and navigate to:
```
http://localhost/Cloud_Route/index.html
```

---

## Database Setup

### Database Structure

The database includes **11 tables** with comprehensive constraints:

| Table | Description |
|-------|-------------|
| `users` | User and admin accounts |
| `routes` | Transit routes and paths |
| `buses` | Vehicle information and tracking |
| `drivers` | Driver details and assignments |
| `bookings` | Seat reservations |
| `reports` | User issue reports |
| `pickup_points` | Transit stop locations |
| `schedules` | Event/Bus timetables |
| `notifications` | System notifications |
| `analytics` | Usage analytics |

---

##  Usage

### For Users

#### 1. Registration & Login
1. Open `index.html`
2. Click "Sign up here"
3. Fill in your details (Name, ID, Email, Phone, Password)
4. Login with your credentials

#### 2. Book a Seat
1. Navigate to "Book Trip" from dashboard
2. Select your route
3. Choose date and time
4. Select vehicle and pickup point
5. Choose available seat from seat map
6. Confirm booking

#### 3. View Schedules
1. Go to "Schedules" page
2. Filter by route if needed
3. View arrival/departure times

---

##  Project Structure

```
CloudRoute/
│
├── index.html                 # Landing page with login/signup
├── dashboard.html             # User dashboard with live map
├── booking.html               # Seat booking page
├── schedule.html              # Transit schedules and timetables
├── report.html                # Issue reporting form
├── admin.html                 # Admin dashboard
├── contact.html               # Contact and support page
│
├── style.css                  # Main stylesheet
├── script.js                  # Frontend JavaScript logic
│
├── manifest.json              # PWA manifest
├── sw.js                      # Service worker
│
├── backend/
│   ├── auth.php               # Authentication endpoints
│   ├── buses.php              # Vehicle management endpoints
│   ├── booking.php            # Booking management endpoints
│   ├── report.php             # Report management endpoints
│   ├── admin.php              # Admin management endpoints
│   │
│   └── classes/               # OOP Classes
│       ├── Database.php       # Database connection
│       ├── User.php           # User & authentication
│       ├── Bus.php            # Fleet operations
│       ├── Booking.php        # Booking operations
│       ├── Report.php         # Report operations
│       └── Admin.php          # Admin operations
│
├── sql/
│   └── cloud_route.sql        # Database schema
│
├── README.md                  # This file
└── SETUP_GUIDE.md            # Detailed setup instructions
```

---

##  Security Features

### 1. Password Security
- **Bcrypt hashing** using `password_hash()`
- Passwords never stored in plain text

### 2. Input Validation
- **Client-side** and **Server-side** validation
- **Database** level constraints and triggers

### 3. SQL Injection Prevention
- Parameterized queries and input sanitization

---

##  Credits

**Developed by**: Cloud Route-GROUP 10 BCS(2024) 
**Project Type**: Intelligent Transportation Management System  
**Year**: 2026 
**Version**: 2.0 (Professional Redesign)

---

##  License

This software is provided for professional transportation management use.

---

##  Support

For issues, questions, or suggestions:
- **Email**: kevinmugenyi57@gmail.com
- **Website**: ...yet to be hosted
---

<div align="center">

**Made with  for better transit**

[⬆ Back to Top](#cloud-route---intelligent-transportation-system)

</div>
