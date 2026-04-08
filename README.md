#  Campus Connect - University Transportation Management System

<div align="center">

![Campus Connect](https://img.shields.io/badge/Campus-Connect-blue?style=for-the-badge)
![Version](https://img.shields.io/badge/version-2.0-green?style=for-the-badge)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-OOP-blue?style=for-the-badge)

**A modern, professional web application for managing university bus transportation**

Built for **Mbarara University of Science and Technology (MUST)**

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

**Campus Connect** is a comprehensive transportation management system designed specifically for university students. It provides:

 **Real-time bus tracking** with interactive maps  
 **Online seat booking** with seat selection  
 **Schedule management** for all routes  
 **Issue reporting** system  
 **Admin dashboard** for system management  
 **PWA support** - Install as mobile app  

---

## Features

### For Students
-  **Live Bus Tracking** - See bus locations in real-time on an interactive map
-  **Easy Booking** - Reserve seats with visual seat selection
-  **Schedule Viewing** - Check bus timetables and routes
-  **Report Issues** - Submit feedback and report problems
-  **Mobile Responsive** - Works perfectly on all devices
-  **Notifications** - Get updates about your bookings

### For Administrators
-  **User Management** - Manage student accounts
-  **Bus Management** - Add, edit, and monitor buses
-  **Driver Management** - Assign and manage drivers
-  **Analytics Dashboard** - View system statistics
-  **Report Management** - Handle student reports
-  **Booking Overview** - Monitor all bookings

---

##  Tech Stack

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with gradients and animations
- **Bootstrap 5.3** - Responsive UI framework
- **JavaScript (ES6+)** - Interactive functionality
- **Font Awesome 6** - Professional icon library
- **Leaflet.js** - Interactive maps (no API key needed!)

### Backend
- **PHP 7.4+** - Object-Oriented Programming
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
git clone https://github.com/your-repo/campus-connect.git

# Or download ZIP and extract to:
C:\xampp\htdocs\CampusConnect
```

#### 2. Start XAMPP Services
1. Open **XAMPP Control Panel**
2. Start **Apache** (Port 80)
3. Start **MySQL** (Port 3306)
4. Verify both show "Running" in green

#### 3. Import Database
**Method 1: Using phpMyAdmin**
1. Open browser: `http://localhost/phpmyadmin`
2. Click "New" → Database name: `campus_connect`
3. Collation: `utf8mb4_unicode_ci` → Click "Create"
4. Click on `campus_connect` database
5. Click "Import" tab
6. Choose file: `sql/campus_connect.sql`
7. Click "Go" at bottom
8. Wait for success message 

**Method 2: Using Command Line**
```bash
mysql -u root -p
CREATE DATABASE campus_connect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE campus_connect;
SOURCE C:/xampp/htdocs/CampusConnect/sql/campus_connect.sql;
EXIT;
```

#### 4. Configure Database Connection
Open `backend/classes/Database.php` and verify:
```php
private $host = 'localhost';
private $username = 'root';
private $password = '';  // Empty for default XAMPP
private $database = 'campus_connect';
```

#### 5. Access the Application
Open your browser and navigate to:
```
http://localhost/CampusConnect/index.html
```

---

## Database Setup

### Database Structure

The database includes **11 tables** with comprehensive constraints:

| Table | Description |
|-------|-------------|
| `users` | Student and admin accounts |
| `routes` | Bus routes and paths |
| `buses` | Bus information and GPS tracking |
| `drivers` | Driver details and assignments |
| `bookings` | Seat reservations |
| `reports` | Student issue reports |
| `pickup_points` | Bus stop locations |
| `schedules` | Bus timetables |
| `notifications` | System notifications |
| `analytics` | Usage analytics |

### Database Constraints

 **CHECK Constraints** - Data validation at database level
```sql
-- Examples:
CONSTRAINT chk_email_format CHECK (email REGEXP '^[A-Za-z0-9._%+-]+@...')
CONSTRAINT chk_capacity_range CHECK (capacity BETWEEN 10 AND 100)
CONSTRAINT chk_future_date CHECK (date >= CURDATE())
```

 **FOREIGN KEY Constraints** - Referential integrity
```sql
-- Examples:
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE SET NULL
```

 **UNIQUE Constraints** - Prevent duplicates
```sql
-- Examples:
username VARCHAR(50) NOT NULL UNIQUE
UNIQUE KEY unique_active_booking (bus_number, date, time, seat_number)
```

 **Database Triggers** - Business logic enforcement
```sql
-- Prevents double booking of same seat
CREATE TRIGGER tr_prevent_double_booking BEFORE INSERT ON bookings

-- Validates bus capacity
CREATE TRIGGER tr_validate_booking_capacity BEFORE INSERT ON bookings

-- Updates GPS timestamp
CREATE TRIGGER tr_bus_location_update BEFORE UPDATE ON buses
```

### Sample Data Included

The SQL file includes sample data for testing:
- **1 Admin user** (username: `admin`, password: `admin123`)
- **3 Student users** (username: `2024001/2024002/2024003`, password: `password`)
- **5 Buses** with different routes
- **4 Drivers** assigned to buses
- **8 Pickup points** around campus
- **Multiple schedules** for different times

---

##  Usage

### For Students

#### 1. Registration & Login
1. Open `index.html`
2. Click "Sign up here"
3. Fill in your details:
   - Full Name
   - Student ID (username)
   - Email (university email)
   - Phone number
   - Password (min 8 characters)
4. Agree to terms and click "Create Account"
5. Login with your credentials

#### 2. Book a Seat
1. Navigate to "Book Seat" from dashboard
2. Select your route (e.g., Campus → Town Center)
3. Choose date and time
4. Select bus from available options
5. Choose pickup point
6. Click on available seat (green) from seat map
7. Review booking summary
8. Click "Confirm Booking"

#### 3. View Schedules
1. Go to "Schedules" page
2. Filter by route if needed
3. View departure times for all buses
4. Click "Book" to reserve seat

#### 4. Report Issue
1. Navigate to "Report Issue"
2. Select issue type (delay, overcrowding, safety, etc.)
3. Choose priority level
4. Fill in details (route, bus, date, time, location)
5. Provide detailed description
6. Choose contact method
7. Submit report

#### 5. Track Buses
1. Go to Dashboard
2. View live map showing:
   - **Red markers** = Active buses
   - **Blue markers** = Pickup points
3. Click markers for details
4. Use "Refresh" to update locations
5. Use "Center" to recenter map

### For Administrators

#### 1. Admin Login
1. Open `admin.html`
2. Enter admin credentials:
   - Username: `admin`
   - Password: `admin123`
3. Click "Login as Admin"

#### 2. Manage Buses
1. Go to "Manage Buses" tab
2. View all buses with status
3. Click "+ Add New Bus" to add
4. Edit or delete existing buses
5. Monitor bus locations

#### 3. Manage Drivers
1. Switch to "Manage Drivers" tab
2. View all drivers
3. Add new drivers
4. Assign drivers to buses
5. Update driver status

#### 4. View Bookings
1. Go to "View Bookings" tab
2. See all today's bookings
3. Filter by status
4. Export data if needed

#### 5. Handle Reports
1. Switch to "View Reports" tab
2. See pending reports (high priority first)
3. Click "View Details" to read
4. Mark as resolved when fixed

---

##  Project Structure

```
CampusConnect/
│
├── index.html                 # Landing page with login/signup
├── dashboard.html             # Student dashboard with live map
├── booking.html               # Seat booking page
├── schedule.html              # Bus schedules and timetables
├── report.html                # Issue reporting form
├── admin.html                 # Admin dashboard
├── contact.html               # Contact and support page
│
├── style.css                  # Main stylesheet with Bootstrap enhancements
├── script.js                  # Frontend JavaScript logic
│
├── manifest.json              # PWA manifest for installability
├── sw.js                      # Service worker for offline support
│
├── backend/
│   ├── db.php                 # Database connection (legacy)
│   ├── auth.php               # Authentication endpoints
│   ├── buses.php              # Bus management endpoints
│   ├── booking.php            # Booking management endpoints
│   ├── report.php             # Report management endpoints
│   ├── admin.php              # Admin management endpoints
│   │
│   └── classes/               # OOP Classes
│       ├── Database.php       # Database connection (Singleton)
│       ├── User.php           # User authentication & management
│       ├── Bus.php            # Bus CRUD operations
│       ├── Booking.php        # Booking operations
│       ├── Report.php         # Report operations
│       └── Admin.php          # Admin operations
│
├── sql/
│   └── campus_connect.sql     # Database schema with constraints
│
├── README.md                  # This file
└── SETUP_GUIDE.md            # Detailed setup instructions
```

---

## 🔌 API Endpoints

### Authentication (`backend/auth.php`)

#### Login
```
POST /backend/auth.php?action=login
Body: username, password
Response: {success: true, user: {...}}
```

#### Signup
```
POST /backend/auth.php?action=signup
Body: name, username, email, phone, password
Response: {success: true, user_id: 123}
```

#### Logout
```
GET /backend/auth.php?action=logout
Response: {success: true, message: "Logged out"}
```

### Buses (`backend/buses.php`)

#### Get Bus List
```
GET /backend/buses.php?action=list
Response: {success: true, buses: [{...}, {...}]}
```

#### Get Bus Locations (for map)
```
GET /backend/buses.php?action=locations
Response: {success: true, locations: [{bus_number, lat, lng}, ...]}
```

#### Get Bus Availability
```
GET /backend/buses.php?action=availability&route=campus-town&date=2024-10-11&time=07:00:00
Response: {success: true, buses: [{bus_number, capacity, available_seats}, ...]}
```

### Bookings (`backend/booking.php`)

#### Create Booking
```
POST /backend/booking.php?action=create
Body: user_id, bus, route, date, time, pickup, seat
Response: {success: true, booking_id: "BC1728..."}
```

#### Cancel Booking
```
POST /backend/booking.php?action=cancel
Body: booking_id
Response: {success: true, message: "Booking cancelled"}
```

#### Get User Bookings
```
GET /backend/booking.php?action=list&user_id=123
Response: {success: true, bookings: [{...}, {...}]}
```

### Reports (`backend/report.php`)

#### Create Report
```
POST /backend/report.php?action=create
Body: user_id, issue_type, priority, route, description, etc.
Response: {success: true, report_id: "RPT1728..."}
```

#### Get Reports
```
GET /backend/report.php?action=list&status=pending
Response: {success: true, reports: [{...}, {...}]}
```

#### Resolve Report
```
POST /backend/report.php?action=resolve
Body: report_id, resolution
Response: {success: true, message: "Report resolved"}
```

### Admin (`backend/admin.php`)

#### Admin Login
```
POST /backend/admin.php?action=login
Body: username, password
Response: {success: true, admin: {...}}
```

#### Get Statistics
```
GET /backend/admin.php?action=stats
Response: {success: true, stats: {total_users, total_buses, ...}}
```

#### Add/Update/Delete Bus
```
POST /backend/admin.php?action=add_bus
POST /backend/admin.php?action=update_bus
POST /backend/admin.php?action=delete_bus
```

#### Add/Update/Delete Driver
```
POST /backend/admin.php?action=add_driver
POST /backend/admin.php?action=update_driver
POST /backend/admin.php?action=delete_driver
```

---

## 🔒 Security Features

### 1. Password Security
- **Bcrypt hashing** using `password_hash()`
- Minimum 8 characters required
- Passwords never stored in plain text

### 2. Input Validation
- **Client-side** validation with HTML5 and JavaScript
- **Server-side** validation in PHP classes
- **Database** constraints and triggers

### 3. SQL Injection Prevention
- Parameterized queries (where applicable)
- Input sanitization using `real_escape_string()`
- `htmlspecialchars()` to prevent XSS

### 4. Session Management
- Secure session handling
- Session data stored in localStorage (frontend)
- Session validation on protected pages

### 5. Data Integrity
- Foreign key constraints ensure referential integrity
- CHECK constraints validate data format
- Triggers prevent invalid operations

---

## 📸 Screenshots

### Student Login Page
Modern gradient design with Bootstrap 5 components

### Dashboard with Live Map
Real-time bus tracking using Leaflet.js

### Seat Booking Interface
Interactive seat selection with visual feedback

### Admin Dashboard
Comprehensive management interface with tabs

---

## 🎨 Design Features

### Color Scheme
- **Primary Blue**: #2563eb - Trust, professionalism
- **Purple Gradient**: #667eea → #764ba2 - Modern, energetic
- **Success Green**: #10b981 - Positive actions
- **Danger Red**: #ef4444 - Alerts, warnings

### Typography
- **Font Family**: Inter, Segoe UI, System fonts
- **Headings**: Bold, large, gradient effects
- **Body**: Clear, readable, 1.6 line-height

### Components
- **Gradient backgrounds** on headers
- **Smooth transitions** on all interactive elements
- **Shadow effects** for depth and hierarchy
- **Hover animations** for better UX
- **Responsive breakpoints** for all screen sizes

---

## 🌐 Browser Support

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

---

## 📱 PWA Features

### Installable
Users can install Campus Connect as a mobile app:
1. Open site in browser
2. Click "Add to Home Screen" (mobile) or install icon (desktop)
3. App opens in standalone mode

### Offline Support
Service worker caches:
- HTML pages
- CSS stylesheets
- JavaScript files
- Static assets

### Mobile Optimized
- Touch-friendly interface
- Responsive design
- Fast loading
- Native-like experience

---

## 🐛 Troubleshooting

### Database Connection Failed
**Solution**: Check XAMPP MySQL is running, verify credentials in `Database.php`

### Map Not Loading
**Solution**: Check internet connection (Leaflet loads from CDN), verify JavaScript console for errors

### Login Not Working
**Solution**: Verify database was imported correctly, check browser console, clear cache

### Booking Not Saving
**Solution**: Check user is logged in, verify database constraints aren't violated

### Admin Page Not Accessible
**Solution**: Use correct credentials (admin/admin123), check `admin.php` exists

---

## 🚀 Future Enhancements

- [ ] Real GPS integration with actual buses
- [ ] Push notifications for bookings
- [ ] Payment gateway integration
- [ ] Mobile app (React Native/Flutter)
- [ ] SMS notifications
- [ ] QR code ticket scanning
- [ ] Route optimization algorithms
- [ ] Predictive analytics for demand

---

## 👥 Credits

**Developed by**: MUST Computer Science Students  
**University**: Mbarara University of Science and Technology  
**Project Type**: University Transportation Management System  
**Year**: 2024
**Version**: 2.0 (Professional Redesign)

### Technologies Used
- Bootstrap 5.3 - UI Framework
- Font Awesome 6 - Icon Library
- Leaflet.js - Interactive Maps
- PHP 7.4+ - Backend
- MySQL 5.7+ - Database

---

## 📄 License

This project is developed for educational purposes as part of the Computer Science curriculum at Mbarara University of Science and Technology.

---

## 📞 Support

For issues, questions, or suggestions:
- **Email**: transport@must.ac.ug
- **Phone**: +256 700 000 000
- **Office**: Transport Office, Administration Block, MUST

---

<div align="center">

**Made with ❤️ by MUST Computer Science Students**

[⬆ Back to Top](#-campus-connect---university-transportation-management-system)

</div>
