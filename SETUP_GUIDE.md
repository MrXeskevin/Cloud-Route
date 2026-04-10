# 🚀 Cloud Route - Complete Setup Guide

## 📋 Project Overview

Cloud Route is a professional, feature-rich web-based transportation management system designed for modern transit operations. This version features:

✅ **Bootstrap 5** - Professional UI framework  
✅ **Font Awesome 6** - Modern icon library  
✅ **Comprehensive Data Integrity** - Foreign Keys, CHECK constraints, and triggers  
✅ **Multi-layer Validation** - Client-side, server-side, and database-level validation  
✅ **Interactive Maps** - Leaflet.js with real-time vehicle tracking  
✅ **Clean Architecture** - OOP PHP backend with Singleton pattern  
✅ **PWA Support** - Installable as a progressive web application  

---

## 🛠️ Installation and Configuration

### Step 1: Prepare Environment
1. Ensure the project files are located in your web server directory (e.g., `D:\Cloud_Route` or `C:\xampp\htdocs\Cloud_Route`).
2. Verify the directory structure:
   - `backend/classes/` - Core logic
   - `sql/` - Database schema
   - `assets/` (if any) or standard HTML/CSS/JS files at the root.

### Step 2: Start Services
1. Open your web server control panel (e.g., **XAMPP Control Panel**).
2. Start the **Apache** and **MySQL** services.
3. Ensure both indicators are green.

### Step 3: Database Implementation

#### Method 1: phpMyAdmin (Automated)
1. Navigate to: `http://localhost/phpmyadmin`
2. Click **"New"** to create a new database.
3. Database Name: `cloud_route`
4. Collation: `utf8mb4_unicode_ci`
5. Click **"Create"**.
6. Select the `cloud_route` database from the sidebar.
7. Click the **"Import"** tab.
8. Choose file: `sql/cloud_route.sql`.
9. Click **"Go"** to execute the schema import.

#### Method 2: Manual SQL Execution
1. Create the database using the command: `CREATE DATABASE cloud_route;`
2. Import the schema: `mysql -u [username] -p cloud_route < sql/cloud_route.sql`

### Step 4: System Configuration

Open `backend/classes/Database.php` and verify the connection parameters:

```php
private $host = 'localhost';
private $username = 'root';
private $password = ''; // Default XAMPP password is empty
private $database = 'cloud_route';
```

---

## 🔑 Access Credentials

### Administrator Access
- **URL**: `http://localhost/Cloud_Route/admin.html`
- **Username**: `admin`
- **Password**: `admin123`

### Standard User Access
- **URL**: `http://localhost/Cloud_Route/index.html`
- **Sample Account 1**: `user101` / `password`
- **Sample Account 2**: `user102` / `password`

---

## ✅ System Verification Checklist

### 1. Authentication Layer
- [ ] Test login with intentionally incorrect credentials (should provide error feedback).
- [ ] Test successful login for both Admin and User tiers.
- [ ] Test session persistence and secure logout.

### 2. Fleet Monitoring
- [ ] Ensure the interactive map loads correctly on the dashboard.
- [ ] Verify that vehicle markers (Red) and transit stops (Blue) appear.
- [ ] Test real-time refresh and recentering functionality.

### 3. Reservation System
- [ ] Select a route and verify that available vehicles are listed.
- [ ] Test the visual seat selection map.
- [ ] Verify that confirmed bookings appear in the management dashboard.

### 4. Data Validation
- [ ] Verify client-side feedback for malformed email addresses or phone numbers.
- [ ] Confirm that password confirmation matching is enforced.
- [ ] Verify that database-level constraints prevent invalid data entry (e.g., historical dates).

---

## 📁 System Architecture

```text
CloudRoute/
│
├── index.html                 # Main landing / Auth entry
├── dashboard.html             # Real-time transit monitoring
├── booking.html               # Reservation management
├── schedule.html              # Transit timetables
├── report.html                # Incident reporting
├── admin.html                 # Administrative control panel
├── contact.html               # Support and FAQ
│
├── style.css                  # Global design system
├── script.js                  # Frontend logic & Map integration
│
├── manifest.json              # PWA manifest
├── sw.js                      # Service worker logic
│
├── backend/                   # API Endpoints
│   └── classes/               # Core OOP Library
│
├── sql/                       # Database Resources
│   └── cloud_route.sql       # Version 2.0 Master Schema
```

---

## 🔧 Troubleshooting

| Issue | Resolution |
|-------|------------|
| **Connection Error** | Ensure MySQL is running and `Database.php` credentials match your environment. |
| **Map Not Centering** | Check for internet connectivity (required for Leaflet CDN) or console errors. |
| **PWA Not Installing** | Ensure you are accessing the site via a valid host (localhost or HTTPS). |
| **Changes Not Visible** | Clear browser cache (Ctrl+F5) to reload latest assets and service worker. |

---

**Cloud Route is now ready for professional deployment.** 🚀
