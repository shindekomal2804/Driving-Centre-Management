# DriveEasy - Driving School Management System

A modern, responsive, full-featured driving school website with user panel and admin dashboard built with PHP, MySQL, MDBootstrap, and vanilla JavaScript.

## 🎯 Features

### User Side (Frontend)
- **Home Page** - Hero section, services, testimonial slider, instructor highlights
- **About Us** - School history, mission, vision, and achievements
- **Courses Page** - List of driving courses (Beginner, Intermediate, Advanced) with filtering
- **Registration & Login** - User registration with email verification, secure login with password encryption
- **Book Driving Lesson** - Calendar-based booking system with instructor and time slot selection
- **User Dashboard** - Profile management, enrolled courses, booking history, lesson cancellation/rescheduling
- **Contact Page** - Contact form, Google Maps integration, business hours
- **Testimonials** - User reviews and ratings
- **Responsive Design** - Mobile-friendly layout using MDBootstrap

### Admin Side (Backend)
- **Admin Login** - Secure authentication system
- **Dashboard Overview** - Statistics, graphs, and analytics (users, bookings, revenue)
- **Manage Users** - View, edit, delete users; block/unblock functionality
- **Manage Courses** - Add/edit/delete courses, set pricing and duration
- **Manage Bookings** - View all bookings, approve/reject/reschedule
- **Instructor Management** - Add/edit/delete instructors, assign schedules
- **Reviews Management** - Approve or delete user reviews
- **Content Management** - Update site information and settings
- **Reports** - Generate booking reports, export data (CSV)

## 📋 Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **UI Framework**: MDBootstrap v6.4.2
- **Icons**: FontAwesome 6.4.0
- **Fonts**: Google Fonts (Poppins)
- **Backend**: Core PHP
- **Database**: MySQL
- **Charts**: Chart.js
- **Security**: bcrypt password hashing, prepared statements, session management

## 📁 Project Structure

```
sj/
├── admin/                      # Admin dashboard pages
│   ├── dashboard.php          # Admin main dashboard
│   ├── users.php              # Manage users
│   ├── courses.php            # Manage courses
│   ├── instructors.php        # Manage instructors
│   ├── bookings.php           # Manage bookings
│   ├── reviews.php            # Manage reviews
│   ├── messages.php           # Contact messages
│   └── settings.php           # Admin settings
├── user/                       # User dashboard pages
│   ├── dashboard.php          # User dashboard
│   ├── profile.php            # User profile
│   └── bookings.php           # User bookings
├── api/                        # API handlers
│   ├── register.php           # User registration
│   ├── login.php              # User login
│   ├── admin-login.php        # Admin login
│   ├── logout.php             # User logout
│   ├── admin-logout.php       # Admin logout
│   └── contact.php            # Contact form handler
├── config/                     # Configuration files
│   ├── database.php           # Database connection
│   └── constants.php          # Application constants
├── includes/                   # Reusable components
│   ├── auth.php               # Authentication class
│   ├── user-header.php        # User page header
│   ├── user-footer.php        # User page footer
│   ├── admin-header.php       # Admin page header
│   └── admin-footer.php       # Admin page footer
├── assets/                     # Static assets
│   ├── css/
│   │   └── style.css          # User frontend CSS
│   │   └── admin-style.css    # Admin dashboard CSS
│   ├── js/
│   │   ├── script.js          # User frontend JavaScript
│   │   └── admin-script.js    # Admin dashboard JavaScript
│   └── images/                # Images folder
├── database/                   # Database files
│   └── schema.sql             # Database schema
├── index.php                   # Homepage
├── register.php               # User registration page
├── login.php                  # User login page
├── admin-login.php            # Admin login page
├── courses.php                # Courses listing page
├── contact.php                # Contact page
└── README.md                  # This file
```

## 🚀 Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP or similar local server
- Modern web browser

### Step 1: Clone/Download Project
```bash
# Place the project in xampp/htdocs/
# Navigate to the folder
cd c:\xampp\htdocs\sj
```

### Step 2: Create Database
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Import the database schema:
   - Click "Import" tab
   - Select `database/schema.sql`
   - Click "Go"

Or manually execute the SQL:
```sql
# Execute queries from database/schema.sql
```

### Step 3: Configure Database Connection
Edit `config/database.php` and update credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your password
define('DB_NAME', 'driving_school');
```

### Step 4: Start Server
```bash
# Start XAMPP Apache and MySQL services
# Navigate to http://localhost/sj
```

### Step 5: Default Credentials
**Admin Login:**
- Username: `admin`
- Password: `123456` (hash: `$2y$10$dmE9Z7r6YU0HYQpfJy3K3eKvFjHLfXCTHHQqmS0kVx1w5R0XCU3Ka`)

**Test User:**
- Email: `user@example.com`
- Password: `123456`

## 📊 Database Schema

### Tables Created
- `users` - User accounts and profiles
- `courses` - Course information and pricing
- `instructors` - Instructor profiles
- `enrollments` - Course enrollments
- `bookings` - Lesson bookings
- `reviews` - User ratings and feedback
- `admin` - Admin user accounts
- `testimonials` - Homepage testimonials
- `contact_messages` - Contact form submissions
- `payments` - Payment transactions
- `site_settings` - Configuration settings

## 🔐 Security Features

- **Password Hashing**: bcrypt with cost 10
- **SQL Injection Prevention**: Prepared statements with parameterized queries
- **Session Management**: Secure session handling with timeout
- **Authentication**: Required login for protected pages
- **Input Validation**: Client and server-side validation
- **CSRF Protection**: Token-based form submission (ready to implement)

## 🎨 UI/UX Design

- **Color Scheme**: Blue (#1e63d9), Yellow (#ffc107), White
- **Typography**: Google Fonts Poppins
- **Components**: MDBootstrap cards, modals, forms
- **Icons**: FontAwesome 6.4.0
- **Responsive**: Mobile-first design, works on all devices
- **Animations**: Smooth transitions and hover effects

## 📱 Pages Overview

### User Pages
1. **index.php** - Homepage with hero, services, courses, testimonials
2. **register.php** - User registration form
3. **login.php** - User login form
4. **courses.php** - All courses with filtering
5. **contact.php** - Contact form and information
6. **user/dashboard.php** - User dashboard with stats
7. **user/profile.php** - Profile edit and password change
8. **user/bookings.php** - View and manage bookings

### Admin Pages
1. **admin-login.php** - Admin login
2. **admin/dashboard.php** - Admin dashboard with analytics
3. **admin/users.php** - User management
4. **admin/courses.php** - Course management
5. **admin/instructors.php** - Instructor management
6. **admin/bookings.php** - Booking management
7. **admin/reviews.php** - Review management
8. **admin/messages.php** - Contact message management
9. **admin/settings.php** - Site settings

## 🔄 API Endpoints

### User APIs
- `POST /api/register.php` - Register new user
- `POST /api/login.php` - User login
- `GET /api/logout.php` - User logout
- `POST /api/contact.php` - Submit contact form

### Admin APIs
- `POST /api/admin-login.php` - Admin login
- `GET /api/admin-logout.php` - Admin logout

## ⚙️ Configuration

### Database Configuration
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'driving_school');
```

### Site Configuration
Edit `config/constants.php`:
```php
define('SITE_NAME', 'DriveEasy - Driving School');
define('SITE_URL', 'http://localhost/sj');
define('SESSION_TIMEOUT', 3600);  // 1 hour
```

### Email Configuration
Edit `config/constants.php` for email notifications:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
```

## 📚 Code Conventions

- **PHP**: PSR-12 coding standards
- **Naming**: camelCase for functions, snake_case for database fields
- **Comments**: Clear, descriptive comments for complex logic
- **Indentation**: 4 spaces
- **Line Length**: Reasonable length, max 120 characters

## 🧪 Testing

### Test User Registration
1. Navigate to /register.php
2. Fill in form with valid data
3. Submit and check database

### Test Admin Login
1. Navigate to /admin-login.php
2. Enter: admin / 123456
3. Should redirect to dashboard

### Test Booking System
1. Login as user
2. Go to Courses
3. Click "Enroll Now"
4. Complete booking form

## 🐛 Debugging

Enable error reporting in `config/constants.php`:
```php
define('SHOW_ERRORS', true);
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

Check logs in server error log for issues.

## 🚀 Deployment

### Production Checklist
- [ ] Disable error display (`SHOW_ERRORS = false`)
- [ ] Use environment variables for secrets
- [ ] Set strong admin password
- [ ] Configure SSL certificate
- [ ] Set up automated backups
- [ ] Configure email notifications
- [ ] Test all payment gateways
- [ ] Optimize database queries
- [ ] Set up CDN for static assets
- [ ] Configure caching headers

### Deployment Steps
1. Upload files to server
2. Set proper file permissions (755 for dirs, 644 for files)
3. Configure database on production server
4. Update `config/constants.php` with production URLs
5. Run `database/schema.sql` on production database
6. Test all features
7. Enable HTTPS

## 📦 Optional Features to Add

- [ ] Payment gateway integration (Razorpay/Stripe)
- [ ] SMS notifications (Twilio)
- [ ] Email notifications
- [ ] Live chat support
- [ ] Multi-language support
- [ ] Student certificates generation
- [ ] Progress tracking
- [ ] Online test system
- [ ] Document upload/verification
- [ ] Attendance tracking
- [ ] Performance analytics

## 🤝 Contributing

This is a complete project template. Feel free to:
- Customize styling
- Add more features
- Integrate payment systems
- Add more database fields
- Optimize performance

## 📝 License

This project is provided as-is for educational and commercial use.

## 💡 Support & Documentation

For issues or questions:
1. Check the database schema in `database/schema.sql`
2. Review the authentication class in `includes/auth.php`
3. Check error logs in browser console or server logs
4. Review code comments throughout the project

## 🎓 Learning Resources

- [MDBootstrap Documentation](https://mdbootstrap.com/)
- [PHP Official Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [FontAwesome Icons](https://fontawesome.com/icons)
- [Chart.js Documentation](https://www.chartjs.org/)

## 📞 Quick Reference

### Important Files
- **Database Connection**: `config/database.php`
- **Authentication**: `includes/auth.php`
- **Database Schema**: `database/schema.sql`
- **Admin Header**: `includes/admin-header.php`
- **User Header**: `includes/user-header.php`

### Key Functions
- `getConnection()` - Get database connection
- `getRows()` - Fetch multiple rows
- `getRow()` - Fetch single row
- `AuthManager::isUserLoggedIn()` - Check user login
- `AuthManager::isAdminLoggedIn()` - Check admin login

---

**Version**: 1.0.0  
**Last Updated**: March 2026  
**Created by**: DriveEasy Development Team
