# GUVI Authentication System - Deployment Guide

## GitHub Setup

1. **Initialize Git Repository**
```bash
cd /Users/flowkiqinc/guvi
git init
git add .
git commit -m "Initial commit: GUVI authentication system"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/guvi.git
git push -u origin main
```

2. **Replace `YOUR_USERNAME` with your GitHub username**

## Heroku Deployment

### Prerequisites
- Install Heroku CLI: `brew install heroku`
- Login: `heroku login`

### Deploy Steps

1. **Create Heroku App**
```bash
heroku create your-app-name
```

2. **Add Buildpacks**
```bash
heroku buildpacks:add heroku/php
heroku buildpacks:add heroku/nodejs
```

3. **Set Environment Variables**
```bash
heroku config:set MYSQL_HOST=your-mysql-host
heroku config:set MYSQL_USER=your-mysql-user
heroku config:set MYSQL_PASSWORD=your-mysql-password
heroku config:set MYSQL_DB=guvi

heroku config:set REDIS_HOST=your-redis-host
heroku config:set REDIS_PORT=6379

heroku config:set MONGO_URI=mongodb+srv://username:password@cluster.mongodb.net
heroku config:set MONGO_DB=guvi
```

4. **Deploy**
```bash
git push heroku main
```

5. **Run Setup**
```bash
heroku run php php/setup.php
```

### Recommended Services

**MySQL:** Amazon RDS or ClearDB
**Redis:** Redis Cloud or Heroku Redis
**MongoDB:** MongoDB Atlas (Free tier available)

## Architecture

- **Frontend:** HTML, CSS, JavaScript (jQuery)
- **Backend:** PHP 8.5
- **Databases:**
  - MySQL: User registration
  - MongoDB: User profiles
  - Redis: Session storage
- **Session:** Browser localStorage + Redis backend

## Requirements Met ✅

- Separate HTML, CSS, JS, PHP files
- jQuery AJAX only (no form submission)
- Bootstrap responsive design
- MySQL with prepared statements
- MongoDB for profiles
- Redis for sessions
- localStorage for browser session
- Max 2 fonts, SVG icons only
