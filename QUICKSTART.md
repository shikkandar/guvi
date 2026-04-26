# GUVI Project - Quick Start Guide

## What Has Been Created

Your complete GUVI authentication system is ready! Here's what's included:

### ✅ Project Structure
- Frontend files (HTML, CSS, JavaScript)
- Backend API files (PHP)
- Database configuration
- Session storage system
- Complete documentation

### ✅ Features Implemented
- User Registration (MySQL storage)
- User Login with Session Management
- User Profile Management (MongoDB ready)
- Secure Password Hashing (Bcrypt)
- AJAX-based Forms (No page reload)
- Responsive Bootstrap Design
- Prepared SQL Statements (No SQL Injection)

### ✅ Database Setup
- MySQL database created: `guvi`
- Users table with indexes
- Session storage configured
- MongoDB ready (optional)
- Redis ready (optional, with file-based fallback)

## Get Started in 2 Minutes

### 1. Ensure Services Are Running
```bash
# Start MySQL
brew services start mysql

# (Optional) Start MongoDB
brew services start mongodb-community

# (Optional) Start Redis
brew services start redis
```

### 2. Start Web Server
Choose any one:

**Option A: PHP Built-in Server (Easiest)**
```bash
cd guvi
php -S localhost:8000
```
Then visit: `http://localhost:8000`

**Option B: Apache**
Configure your Apache to point to the guvi folder and visit: `http://localhost/guvi`

**Option C: Nginx**
Configure your Nginx server block to point to the guvi folder

### 3. Test the Application

1. **Register a New User**
   - Go to Register page
   - Fill in Full Name, Email, Password
   - Click Register
   - You should be redirected to Login

2. **Login**
   - Enter the email and password you just registered
   - Click Login
   - You should be redirected to Profile page

3. **Update Profile**
   - Fill in Age, Date of Birth, Contact, Address
   - Click Update Profile
   - You should see a success message

4. **Logout**
   - Click the Logout button
   - You should be redirected to Login page

## Project Files Overview

### Frontend
- `index.html` - Home page
- `register.html` - Registration form
- `login.html` - Login form
- `profile.html` - User profile page
- `css/style.css` - Custom styling
- `js/register.js` - Registration logic
- `js/login.js` - Login logic
- `js/profile.js` - Profile logic

### Backend
- `php/register.php` - Registration endpoint
- `php/login.php` - Login/logout endpoint
- `php/profile.php` - Profile management endpoint
- `php/setup.php` - Database initialization

### Configuration
- `config/db.php` - Database settings
- `composer.json` - PHP dependencies
- `vendor/` - Composer packages (MongoDB client library)

### Session Storage
- `sessions/` - User session files (auto-created)

## Testing Credentials

Use these to test the application:

**Example 1:**
- Name: John Doe
- Email: john@example.com
- Password: password123

**Example 2:**
- Name: Jane Smith
- Email: jane@example.com
- Password: secure456

## Key Features to Test

### Form Validation
- Try registering with weak password (< 6 characters)
- Try registering with invalid email
- Try registering with duplicate email

### Security
- Passwords are hashed with bcrypt
- All SQL queries use prepared statements
- Session tokens are 64-character random strings
- CORS headers are properly configured

### Responsive Design
- Resize browser window
- Forms adapt to mobile screens
- Bootstrap provides responsive layout

## Architecture Highlights

### Security
✅ Password hashing with bcrypt
✅ Prepared SQL statements
✅ Session token validation
✅ Input validation (client & server)
✅ CORS headers configured

### Database Design
✅ MySQL for user authentication
✅ MongoDB for user profiles (flexible schema)
✅ Redis for fast session storage (with fallback)

### Frontend
✅ jQuery AJAX for seamless UX
✅ Bootstrap for responsive design
✅ Client-side validation for quick feedback
✅ Server-side validation for security

### Backend
✅ Proper error handling
✅ JSON API responses
✅ Session management
✅ Prepared statements for all queries

## Common Issues & Solutions

### "Database connection failed"
**Solution:** Make sure MySQL is running
```bash
brew services start mysql
```

### "AJAX request failed"
**Solution:** Check browser console for errors. Ensure:
- PHP server is running
- Correct URL is being called
- Browser allows cross-origin requests

### "Session not persisting"
**Solution:** Check that `sessions/` folder exists and is writable
```bash
ls -la sessions/
chmod 755 sessions/
```

### "Profile data not saving"
**Solution:** MongoDB is optional. If not installed, profiles still work (they're just stored in MongoDB when available)

## Next Steps

### For Production
1. Install MongoDB PHP extension: `pecl install mongodb`
2. Install Redis PHP extension: `pecl install redis`
3. Configure proper error logging
4. Set up HTTPS
5. Move database credentials to environment variables
6. Implement rate limiting

### For Learning
1. Review the code comments
2. Understand the security implementations
3. Modify the UI with your own styling
4. Add more profile fields
5. Implement email verification
6. Add profile picture upload

## File Locations

Your project is located at: `/Users/flowkiqinc/guvi`

Key files:
- Frontend: `index.html`, `register.html`, `login.html`, `profile.html`
- Backend: `php/register.php`, `php/login.php`, `php/profile.php`
- Config: `config/db.php`
- Styles: `css/style.css`
- Scripts: `js/register.js`, `js/login.js`, `js/profile.js`

## Quick Command Reference

```bash
# Navigate to project
cd /Users/flowkiqinc/guvi

# Start PHP server
php -S localhost:8000

# Start MySQL
brew services start mysql

# Check MySQL status
brew services list | grep mysql

# Run setup script
php php/setup.php

# View logs
tail -f /opt/homebrew/var/log/mysql.log
```

## Support Resources

- Full README: `README.md`
- Project structure: This guide
- Code comments: Check PHP and JavaScript files
- Configuration: `config/db.php`

## You're All Set! 🎉

Your GUVI authentication system is ready to use. Start the PHP server and begin testing!

```bash
php -S localhost:8000
```

Then visit `http://localhost:8000` in your browser.

Happy coding!
