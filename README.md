# GUVI Internship Project

A user authentication and profile management system built with HTML, CSS, JavaScript, PHP, MySQL, MongoDB, and Redis.

## Tech Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5, jQuery, AJAX
- **Backend**: PHP 8.5+
- **Databases**:
  - MySQL (User registration & authentication)
  - MongoDB (User profiles)
  - Redis (Session storage - optional, file-based fallback available)

## Project Structure

```
guvi/
├── assets/              # Static files (images, fonts)
├── css/
│   └── style.css       # Custom Bootstrap styling
├── js/
│   ├── login.js        # Login form handling
│   ├── profile.js      # Profile management
│   └── register.js     # Registration form handling
├── php/
│   ├── login.php       # Login & logout logic
│   ├── profile.php     # Profile fetch & update
│   ├── register.php    # User registration
│   └── setup.php       # Database initialization
├── config/
│   └── db.php          # Database configuration
├── sessions/           # Session storage (auto-created)
├── index.html          # Landing page
├── login.html          # Login page
├── profile.html        # User profile page
├── register.html       # Registration page
├── composer.json       # PHP dependencies
└── README.md           # This file
```

## Installation & Setup

### Prerequisites

Make sure you have installed:
- PHP 8.5+
- MySQL
- MongoDB
- Redis (optional)
- Composer

### Step 1: Start Required Services

```bash
# Start MySQL
brew services start mysql

# Start MongoDB
brew services start mongodb-community

# Start Redis (optional but recommended)
brew services start redis
```

### Step 2: Install PHP Dependencies

```bash
# Navigate to project directory
cd guvi

# Install dependencies
composer install --ignore-platform-req=ext-mongodb
```

### Step 3: Configure Database

Edit `config/db.php` and update credentials if needed:

```php
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', '');  // Update if you set a MySQL password
define('MYSQL_DB', 'guvi');
```

### Step 4: Initialize Database

Run the setup script to create MySQL database and tables:

```bash
php php/setup.php
```

You should see output like:
```
Setting up GUVI Application...

1. Setting up MySQL Database...
   ✓ Database created/verified
   ✓ Users table created/verified

2. Setting up MongoDB Connection...
   ✓ Profiles collection ready

3. Checking Redis Connection...
   ✓ Redis is running

4. Setting up Session Storage...
   ✓ Sessions directory created

✓ Setup completed successfully!
```

### Step 5: Start Web Server

Choose one of the following:

**Option 1: PHP Built-in Server**
```bash
php -S localhost:8000
```

**Option 2: Apache (with .htaccess)**
Create `.htaccess` in project root if using Apache

**Option 3: Nginx**
Configure server block to point to project directory

### Step 6: Access the Application

Open your browser and navigate to:
- **PHP Server**: `http://localhost:8000`
- **Apache**: `http://localhost/guvi`

## Features

### Registration
- Create new user account
- Validates email format and password strength
- Stores credentials securely in MySQL (bcrypt hashing)
- Client-side and server-side validation

### Login
- Authenticate with email and password
- Session token stored in browser localStorage
- Session data stored in Redis (with file-based fallback)
- Automatic redirect to profile on successful login

### Profile Management
- View user information
- Update personal details:
  - Age
  - Date of Birth
  - Contact Number
  - Address
- Data stored in MongoDB
- Update profile in real-time

### Session Management
- Secure session tokens (random 64-character hex strings)
- 24-hour session expiration
- Automatic logout
- Redis-backed sessions with file system fallback

## Security Features

1. **Password Security**
   - Bcrypt hashing with salt
   - Minimum 6 characters requirement
   - Verified against stored hash

2. **SQL Injection Prevention**
   - Prepared statements for all MySQL queries
   - Parameter binding for safety

3. **Session Security**
   - Random token generation
   - Secure token storage
   - Session validation on every request

4. **Input Validation**
   - Client-side validation (immediate feedback)
   - Server-side validation (security)
   - Email format validation
   - Phone number validation

5. **CORS Headers**
   - Properly configured for security

## API Endpoints

### Register
- **URL**: `php/register.php`
- **Method**: POST
- **Parameters**:
  ```
  fullname: string
  email: string
  password: string
  ```
- **Response**: JSON with success status and message

### Login
- **URL**: `php/login.php`
- **Method**: POST
- **Parameters**:
  ```
  email: string
  password: string
  ```
- **Response**: JSON with token, email, fullname

### Logout
- **URL**: `php/login.php`
- **Method**: POST
- **Parameters**:
  ```
  action: 'logout'
  sessionToken: string
  ```

### Get Profile
- **URL**: `php/profile.php`
- **Method**: POST
- **Parameters**:
  ```
  action: 'fetch'
  sessionToken: string
  ```

### Update Profile
- **URL**: `php/profile.php`
- **Method**: POST
- **Parameters**:
  ```
  action: 'update'
  sessionToken: string
  age: integer (optional)
  dob: date (optional)
  contact: string (optional)
  address: string (optional)
  ```

## File Descriptions

### Frontend Files
- **index.html**: Landing page with navigation
- **register.html**: User registration form
- **login.html**: User login form
- **profile.html**: User profile view and edit

### JavaScript Files
- **register.js**: Handles registration form submission via AJAX
- **login.js**: Handles login form submission and session storage
- **profile.js**: Handles profile data fetching and updating

### Backend Files
- **register.php**: Creates new user in MySQL
- **login.php**: Authenticates user and creates session in Redis
- **profile.php**: Manages MongoDB profile operations
- **setup.php**: Initializes database structure

### Configuration
- **config/db.php**: Database connection functions and configuration

## Troubleshooting

### MySQL Connection Failed
- Ensure MySQL is running: `brew services list`
- Check credentials in `config/db.php`
- Default root user has no password

### MongoDB Connection Failed
- Ensure MongoDB is running: `brew services list`
- Install MongoDB PHP extension: `pecl install mongodb`
- Or use file-based session storage (already configured as fallback)

### Redis Connection Failed
- Redis is optional; file-based session storage will be used
- To enable Redis: `brew services start redis`

### Sessions Not Persisting
- Ensure `sessions/` directory exists and is writable
- Check file permissions: should be `drwxr-xr-x`

### CORS Errors
- Verify headers are set in `config/db.php`
- Check browser console for error messages

## Development Notes

### Adding New Features
1. Follow existing code structure
2. Use prepared statements for all database queries
3. Implement both client and server validation
4. Use jQuery AJAX for frontend requests
5. Return JSON responses from PHP

### Database Queries
All SQL queries use prepared statements:
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
```

### Session Validation
Always verify session before allowing profile operations:
```php
$sessionData = verifySession($sessionToken);
if (!$sessionData) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Invalid session']));
}
```

## License

This is an internship project for GUVI. All rights reserved.

## Support

For issues or questions, please check the troubleshooting section or review the code comments.
