# Quick Start Guide

## Setup (First Time Only)

### 1. Start MySQL
```bash
brew services start mysql
```

### 2. Initialize Database
```bash
cd /Users/flowkiqinc/guvi
php php/setup.php
```

## Running the App

### 1. Start Web Server
```bash
php -S localhost:8000
```

### 2. Open Browser
```
http://localhost:8000
```

## Test the App

1. **Register Page** (http://localhost:8000/register.html)
   - Email: `test@example.com`
   - Password: `password123`
   - Click Register

2. **Login Page** (http://localhost:8000/login.html)
   - Email: `test@example.com`
   - Password: `password123`
   - Click Login

3. **Profile Page** (redirects after login)
   - Fill: Age, DOB, Contact, Address
   - Click Update Profile

4. **Logout**
   - Click Logout button

## Stop the Server
Press `Ctrl + C` in terminal

## Troubleshooting

| Problem | Solution |
|---------|----------|
| MySQL connection error | `brew services start mysql` |
| Port 8000 already in use | `php -S localhost:8080` |
| Database not found | `php php/setup.php` |

## Project Files

- **HTML**: index.html, register.html, login.html, profile.html
- **JS**: js/register.js, js/login.js, js/profile.js
- **CSS**: css/style.css
- **PHP**: php/register.php, php/login.php, php/profile.php
- **Config**: config/db.php

## Key Features

✅ Registration → MySQL storage
✅ Login → Session token
✅ Profile → MongoDB storage
✅ Responsive → Bootstrap design
✅ Secure → Prepared statements

## Ready!

Your GUVI project is ready to use and submit!
