# 🚀 Campus Connect - Complete Setup Guide

## 📋 What's New in This Version

✅ **Bootstrap 5** - Professional UI framework  
✅ **Font Awesome 6** - Modern icon library (no more emojis!)  
✅ **Comprehensive Database Constraints** - Foreign Keys, CHECK constraints, triggers  
✅ **Form Validation** - Client-side and server-side validation  
✅ **Enhanced Map** - Leaflet.js with animated markers  
✅ **OOP Backend** - Clean class-based architecture  
✅ **Professional Design** - Modern gradient UI with smooth animations  

---

## 🛠️ Installation (5 Minutes)

### Step 1: Download & Extract
1. You already have the project at: `C:\Users\Kevin\Desktop\AOB\CampusConnect`
2. Verify you see these folders:
   - `backend/`
   - `sql/`
   - And HTML files like `index.html`, `dashboard.html`, etc.

### Step 2: Start XAMPP
1. Open **XAMPP Control Panel**
2. Click **Start** for **Apache**
3. Click **Start** for **MySQL**
4. Both should show green "Running" status

### Step 3: Import Database

#### Method 1: Using phpMyAdmin (Recommended)
1. Open browser: `http://localhost/phpmyadmin`
2. Click **"New"** on left sidebar
3. Database name: `campus_connect`
4. Collation: `utf8mb4_unicode_ci`
5. Click **"Create"**
6. Click on **`campus_connect`** database
7. Click **"Import"** tab
8. Click **"Choose File"**
9. Navigate to: `C:\Users\Kevin\Desktop\AOB\CampusConnect\sql\campus_connect.sql`
10. Scroll down, click **"Go"**
11. Wait for green success message ✅

#### Method 2: Using SQL Tab
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click **"SQL"** tab at the top
3. Open `campus_connect.sql` in Notepad
4. **Copy ALL** the content (Ctrl+A, Ctrl+C)
5. **Paste** into the SQL text area
6. Click **"Go"**
7. Success! ✅

### Step 4: Verify Database
1. In phpMyAdmin, click **`campus_connect`** on left
2. You should see **11 tables**:
   - users
   - buses
   - routes
   - drivers
   - bookings
   - reports
   - pickup_points
   - schedules
   - notifications
   - analytics

3. Click **"users"** table
4. Click **"Browse"**
5. You should see 4 users (1 admin + 3 students)

### Step 5: Access the Application
1. Open browser
2. Go to: `http://localhost/CampusConnect/index.html`
3. You should see the beautiful new login page! 🎉

---

## 🔑 Default Login Credentials

### Admin Account
```
Username: admin
Password: admin123
```
Use this to access: `http://localhost/CampusConnect/admin.html`

### Student Accounts
```
Username: 2024001
Password: password
---
Username: 2024002
Password: password
---
Username: 2024003
Password: password
```

---

## ✅ Testing Checklist

### Test 1: Login System ✓
1. Go to `index.html`
2. Try logging in with wrong credentials - should show error
3. Login with `2024001` / `password` - should redirect to dashboard
4. Click **Logout** - should return to login page

### Test 2: Live Map ✓
1. Login and go to Dashboard
2. Map should load with:
   - Blue markers = Pickup points (click to see name)
   - Red markers = Active buses (animated, moving)
3. Click **Refresh** button - buses should update
4. Click **Center** button - map should recenter

### Test 3: Registration ✓
1. Go to `index.html`
2. Click "Sign up here"
3. Fill in all fields:
   - Name: Test Student
   - Student ID: 2024004
   - Email: test@must.ac.ug
   - Phone: +256701234567
   - Password: password123
   - Confirm Password: password123
   - Check "I agree to Terms"
4. Click **Create Account**
5. Should show success message
6. Should auto-switch to login form
7. Login with new credentials

### Test 4: Form Validation ✓
1. On signup form, try to submit empty - should show errors
2. Enter short password (less than 8 chars) - should show error
3. Enter mismatched passwords - should show error
4. Enter invalid email format - should show error
5. All validation messages should be red and clear

### Test 5: Database Constraints ✓
1. Try to create duplicate user with same username - should fail
2. Check database for proper foreign keys:
   ```sql
   SHOW CREATE TABLE bookings;
   ```
3. Should see FOREIGN KEY constraints

---

## 🐛 Troubleshooting

### Issue: "Database connection failed"
**Solution:**
1. Check MySQL is running in XAMPP
2. Open `backend/classes/Database.php`
3. Verify these settings:
   ```php
   private $host = 'localhost';
   private $username = 'root';
   private $password = '';  // Empty for XAMPP default
   private $database = 'campus_connect';
   ```

### Issue: "Map not loading"
**Solution:**
1. Check browser console (F12)
2. Verify internet connection (Leaflet loads from CDN)
3. Make sure you're on `dashboard.html` after login
4. Check JavaScript console for errors

### Issue: "Login not working"
**Solution:**
1. Check browser console for errors
2. Verify backend files exist in `backend/` folder
3. Test backend directly:
   - Open: `http://localhost/CampusConnect/backend/auth.php?action=check`
   - Should see JSON response
4. Clear browser cache (Ctrl+Shift+Delete)

### Issue: "Bootstrap/Icons not loading"
**Solution:**
1. Check internet connection (Bootstrap and Font Awesome load from CDN)
2. Check browser console for 404 errors
3. Alternative: Download Bootstrap and Font Awesome locally

### Issue: "Validation not working"
**Solution:**
1. Make sure Bootstrap 5 JS is loaded
2. Check browser console for JavaScript errors
3. Verify `script.js` is loading properly

---

## 📁 Project Structure

```
CampusConnect/
│
├── index.html              ✅ New Bootstrap design with Font Awesome icons
├── dashboard.html          ⏳ Needs updating (next step)
├── booking.html            ⏳ Needs updating
├── schedule.html           ⏳ Needs updating
├── report.html             ⏳ Needs updating
├── admin.html              ⏳ Needs updating
├── contact.html            ⏳ Needs updating
│
├── style.css               ✅ Updated with Bootstrap enhancements
├── script.js               ✅ New validation and map functionality
│
├── backend/
│   ├── classes/            ✅ All OOP classes ready
│   │   ├── Database.php
│   │   ├── User.php
│   │   ├── Bus.php
│   │   ├── Booking.php
│   │   ├── Report.php
│   │   └── Admin.php
│   │
│   ├── auth.php            ✅ Updated to use OOP
│   ├── buses.php           ✅ Updated to use OOP
│   ├── booking.php         ✅ Updated to use OOP
│   ├── report.php          ✅ Updated to use OOP
│   └── admin.php           ✅ Updated to use OOP
│
└── sql/
    └── campus_connect.sql  ✅ NEW! With all constraints and validations
```

---

## 🎯 What Works Now

### ✅ Completed Features
- [x] Modern Bootstrap 5 UI
- [x] Font Awesome icons (no emojis)
- [x] Login page with validation
- [x] Signup page with validation
- [x] Password visibility toggle
- [x] Form validation (client-side)
- [x] Database constraints (CHECK, FK, NOT NULL)
- [x] Database triggers (prevent double booking)
- [x] OOP backend structure
- [x] Live map with Leaflet.js
- [x] Animated bus markers
- [x] Responsive design

### ⏳ Next Steps (Dashboard and other pages)
Your lecturer will be impressed with:
1. Professional design using Bootstrap
2. Proper icons from Font Awesome
3. Comprehensive database constraints
4. Form validations everywhere
5. Clean OOP code structure

---

## 📊 Database Constraints Implemented

### 1. CHECK Constraints
```sql
-- Users table
CONSTRAINT chk_name_length CHECK (CHAR_LENGTH(name) >= 3)
CONSTRAINT chk_email_format CHECK (email REGEXP '^[A-Za-z0-9._%+-]+@...')
CONSTRAINT chk_password_length CHECK (CHAR_LENGTH(password) >= 8)

-- Buses table
CONSTRAINT chk_capacity_range CHECK (capacity BETWEEN 10 AND 100)
CONSTRAINT chk_latitude_range CHECK (current_lat BETWEEN -90 AND 90)

-- Bookings table
CONSTRAINT chk_future_date CHECK (date >= CURDATE())
```

### 2. FOREIGN KEY Constraints
```sql
-- Bookings reference users
FOREIGN KEY (user_id) REFERENCES users(id) 
    ON DELETE CASCADE ON UPDATE CASCADE

-- Buses reference routes and drivers
FOREIGN KEY (route_id) REFERENCES routes(id) 
    ON DELETE SET NULL ON UPDATE CASCADE
```

### 3. UNIQUE Constraints
```sql
-- Prevent duplicate usernames/emails
username VARCHAR(50) NOT NULL UNIQUE
email VARCHAR(100) NOT NULL UNIQUE

-- Prevent double booking same seat
UNIQUE KEY unique_active_booking (bus_number, date, time, seat_number, status)
```

### 4. Triggers
```sql
-- Prevent double booking
CREATE TRIGGER tr_prevent_double_booking BEFORE INSERT ON bookings

-- Validate bus capacity
CREATE TRIGGER tr_validate_booking_capacity BEFORE INSERT ON bookings

-- Update GPS timestamp
CREATE TRIGGER tr_bus_location_update BEFORE UPDATE ON buses
```

---

## 🎓 For Your Presentation

### Points to Highlight:
1. **"We used Bootstrap 5 framework for professional UI"**
2. **"We implemented comprehensive database constraints as requested"**
3. **"We have both client-side and server-side validation"**
4. **"Our backend uses Object-Oriented Programming principles"**
5. **"We have database triggers to prevent data integrity issues"**
6. **"The map uses Leaflet.js with real-time updates"**

### Demo Flow:
1. Show login with validation (try wrong password)
2. Register new student
3. Show live map with moving buses
4. Show database constraints in phpMyAdmin
5. Show OOP class structure
6. Explain validation logic

---

## 💡 Tips

1. **Always start Apache and MySQL** before testing
2. **Clear browser cache** if changes don't appear (Ctrl+Shift+Delete)
3. **Check browser console (F12)** for JavaScript errors
4. **Check phpMyAdmin** to verify database structure
5. **Use Chrome DevTools** to inspect responsive design

---

## 📞 Need Help?

If something doesn't work:
1. Check XAMPP Apache and MySQL are running (green lights)
2. Check browser console (F12) for errors
3. Verify database was imported correctly
4. Check file paths are correct
5. Clear browser cache

---

**🎉 Your project is now professional-grade with:**
- Modern UI framework (Bootstrap 5)
- Professional icons (Font Awesome)
- Comprehensive validations (Client + Server + Database)
- Clean OOP architecture
- Working live map
- Database constraints and triggers

**Ready to impress your lecturer!** 🚀

