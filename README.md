# Adopet App - Pet Adoption Platform

A full-stack web application for pet adoption built with Laravel (backend) and Nuxt.js (frontend).

## Features

- **User Authentication**: Register, login, and logout functionality
- **Pet Listings**: Browse and search for pets available for adoption
- **Post Pets**: Users can post pets for adoption with images
- **Real-time Chat**: Message pet owners directly
- **Adoption Requests**: Request to adopt pets and manage adoption status
- **User Profiles**: View your posted pets and adoption requests

## Tech Stack

### Backend
- Laravel 12
- Laravel Sanctum (API Authentication)
- SQLite Database (can be changed to MySQL/PostgreSQL)

### Frontend
- Nuxt.js 4
- Vue 3
- Axios for API calls

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- npm or yarn

### Backend Setup

1. Navigate to the backend directory:
```bash
cd backend
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Run migrations:
```bash
php artisan migrate
```

6. Create storage link (for file uploads):
```bash
php artisan storage:link
```

7. Start the development server:
```bash
php artisan serve
```

The backend API will be available at `http://localhost:8000`

### Frontend Setup

1. Navigate to the frontend directory:
```bash
cd frontend
```

2. Install dependencies:
```bash
npm install
```

3. Start the development server:
```bash
npm run dev
```

The frontend will be available at `http://localhost:3000`

## Configuration

### Backend Environment Variables

Edit `backend/.env` and configure:
- Database connection (default: SQLite)
- APP_URL (should match your frontend URL)

### Frontend Configuration

Edit `frontend/nuxt.config.js` to set the API base URL:
```javascript
axios: {
  baseURL: process.env.API_BASE_URL || 'http://localhost:8000/api',
}
```

## Database Schema

- **users**: User accounts with profile information
- **pets**: Pet listings with details and images
- **chats**: Chat conversations between users
- **messages**: Messages within chats
- **adoptions**: Adoption requests and status

## API Endpoints

### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user
- `GET /api/user` - Get current user

### Pets
- `GET /api/pets` - List all pets (with filters)
- `GET /api/pets/my-pets` - Get user's pets
- `POST /api/pets` - Create a new pet listing
- `GET /api/pets/{id}` - Get pet details
- `PUT /api/pets/{id}` - Update pet
- `DELETE /api/pets/{id}` - Delete pet

### Chats
- `GET /api/chats` - Get user's chats
- `POST /api/chats` - Create a new chat
- `GET /api/chats/{id}` - Get chat with messages

### Messages
- `POST /api/messages` - Send a message
- `GET /api/chats/{chatId}/messages` - Get chat messages

### Adoptions
- `GET /api/adoptions` - Get user's adoptions
- `POST /api/adoptions` - Request adoption
- `GET /api/adoptions/{id}` - Get adoption details
- `PUT /api/adoptions/{id}` - Update adoption status

## Usage

1. **Register/Login**: Create an account or login
2. **Browse Pets**: View available pets on the home page
3. **Post a Pet**: Click "Post Pet" to add a pet for adoption
4. **Message Owners**: Click "Message Owner" on a pet's page to start chatting
5. **Request Adoption**: Click "Request Adoption" to submit an adoption request
6. **Manage Adoptions**: Pet owners can approve/reject adoption requests in their profile

## File Storage

Pet images are stored in `backend/storage/app/public/pets`. Make sure to run `php artisan storage:link` to create a symbolic link for public access.

## Development

### Backend
- Run migrations: `php artisan migrate`
- Create a migration: `php artisan make:migration migration_name`
- Create a model: `php artisan make:model ModelName`
- Create a controller: `php artisan make:controller ControllerName`

### Frontend
- Development server: `npm run dev`
- Build for production: `npm run build`
- Generate static site: `npm run generate`

## Notes

- The chat functionality uses polling (checks for new messages every 3 seconds). For production, consider implementing WebSockets or Server-Sent Events.
- Image uploads are limited to 2MB per image. Adjust in `backend/app/Http/Controllers/Api/PetController.php`.
- CORS is configured to allow requests from `http://localhost:3000`. Update in `backend/bootstrap/app.php` for production.

## License

This project is open source and available for educational purposes.

"# AdoPet" 
"# Simon1" 
# FinalProject
# FinalProject
