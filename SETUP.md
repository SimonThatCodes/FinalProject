# Quick Setup Guide

## Prerequisites
- PHP 8.2+ with extensions: pdo, pdo_sqlite, mbstring, xml, curl, zip
- Composer
- Node.js 18+ and npm

## Step-by-Step Setup

### 1. Backend Setup

```bash
cd backend
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve
```

Backend will run on `http://localhost:8000`

### 2. Frontend Setup

Open a new terminal:

```bash
cd frontend
npm install
npm run dev
```

Frontend will run on `http://localhost:3000`

### 3. First Time Usage

1. Open `http://localhost:3000` in your browser
2. Click "Register" to create an account
3. After registration, you'll be logged in automatically
4. Click "Post Pet" to add your first pet listing
5. Browse pets and start chatting with owners!

## Troubleshooting

### Backend Issues

**Migration errors:**
```bash
php artisan migrate:fresh
```

**Storage link issues:**
```bash
php artisan storage:link
```

**Permission issues (Linux/Mac):**
```bash
chmod -R 775 storage bootstrap/cache
```

### Frontend Issues

**Port already in use:**
Change port in `package.json` or use:
```bash
npm run dev -- --port 3001
```

**API connection errors:**
- Make sure backend is running on port 8000
- Check CORS settings in `backend/bootstrap/app.php`
- Verify API URL in `frontend/nuxt.config.js`

## Database

The app uses SQLite by default (database file: `backend/database/database.sqlite`).

To use MySQL/PostgreSQL:
1. Update `backend/.env` with your database credentials
2. Run `php artisan migrate`

## Production Deployment

### Backend
1. Set `APP_ENV=production` in `.env`
2. Run `php artisan config:cache`
3. Run `php artisan route:cache`
4. Set up proper web server (Apache/Nginx)

### Frontend
1. Run `npm run build`
2. Deploy the `.output` directory
3. Update API base URL in production environment

## Features to Test

- ✅ User registration and login
- ✅ Posting pets with images
- ✅ Browsing and searching pets
- ✅ Messaging pet owners
- ✅ Requesting adoptions
- ✅ Managing adoption requests
- ✅ Viewing profile and posted pets

Enjoy your Adopet App! 🐾

