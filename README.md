# GUVI Internship Project

User authentication system with registration, login, and profile management.

## Tech Stack

- **Frontend**: HTML, CSS, Bootstrap 5, jQuery AJAX
- **Backend**: PHP
- **Databases**: MySQL (users), MongoDB (profiles), Redis (sessions)

## Project Structure

```
guvi/
├── index.html              # Home page
├── register.html           # Register page
├── login.html              # Login page
├── profile.html            # Profile page
├── css/style.css           # Styling
├── js/
│   ├── register.js         # Register AJAX
│   ├── login.js            # Login AJAX
│   └── profile.js          # Profile AJAX
├── php/
│   ├── register.php        # Register API
│   ├── login.php           # Login API
│   ├── profile.php         # Profile API
│   └── setup.php           # Database setup
├── config/db.php           # Database config
├── composer.json           # PHP packages
└── .gitignore              # Git config
```

## Quick Start

### 1. Start MySQL
```bash
brew services start mysql
```

### 2. Setup Database
```bash
cd /Users/flowkiqinc/guvi
php php/setup.php
```

### 3. Start Web Server
```bash
php -S localhost:8000
```

### 4. Open in Browser
```
http://localhost:8000
```

## Features

✅ User Registration (MySQL)
✅ Secure Login (bcrypt hashing)
✅ Session Management (24-hour expiry)
✅ Profile Management (MongoDB)
✅ Responsive Design (Bootstrap)
✅ AJAX Forms (no page refresh)

## Testing

1. **Register** - Create new account
2. **Login** - Log in with email/password
3. **Profile** - Add age, DOB, contact, address
4. **Logout** - End session

## Security

✅ Prepared SQL statements (no SQL injection)
✅ Bcrypt password hashing
✅ Session token validation
✅ Input validation (client & server)
✅ CORS headers configured

## Requirements Met

✅ HTML, JS, CSS, PHP in separate files
✅ jQuery AJAX only
✅ Bootstrap responsive
✅ MySQL prepared statements
✅ MongoDB for profiles
✅ localStorage for sessions
✅ Redis for backend
✅ 2 fonts maximum
✅ SVG icons only

## Troubleshooting

**MySQL won't connect?**
```bash
brew services start mysql
```

**Port 8000 in use?**
```bash
php -S localhost:8080
```

**Database error?**
```bash
php php/setup.php
```

## File Descriptions

| File | Purpose |
|------|---------|
| index.html | Home page |
| register.html | User registration form |
| login.html | User login form |
| profile.html | User profile (post-login) |
| css/style.css | All styling |
| js/register.js | Registration AJAX |
| js/login.js | Login AJAX |
| js/profile.js | Profile AJAX |
| php/register.php | Register API endpoint |
| php/login.php | Login/logout API |
| php/profile.php | Profile API |
| php/setup.php | Database initialization |
| config/db.php | DB configuration |

## Database Schema

**MySQL - users table**
```
id (PRIMARY KEY)
fullname
email (UNIQUE)
password (hashed)
created_at
updated_at
```

**MongoDB - profiles collection**
```
user_id
email
fullname
age
dob
contact
address
updated_at
```

**Redis - sessions**
```
session_<token> : {user_id, email, fullname, created_at}
Expires after 24 hours
```

## Deployment

Push to GitHub:
```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/username/guvi.git
git push -u origin main
```

Deploy to Heroku:
```bash
heroku create app-name
git push heroku main
```

## Support

See QUICKSTART.md for more details.
