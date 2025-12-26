# 📚 Adopet App - Complete Application Structure Guide

## 🗂️ Project Structure Overview

```
Adopet_App/
├── frontend/          # Nuxt.js 4 Frontend (Vue.js)
├── backend/           # Laravel 11 Backend (PHP)
└── Documentation files
```

---

## 🎨 FRONTEND STRUCTURE

### 📍 Frontend Pages Location
**All frontend pages are in: `frontend/pages/`**

```
frontend/pages/
├── index.vue              # Dashboard/Home page (pet listing)
├── login.vue              # User login page
├── register.vue           # User registration page
├── verification.vue       # Account verification page
├── profile.vue            # User profile page
├── notifications.vue      # Notifications page
├── pets/
│   ├── index.vue          # All pets listing page
│   ├── [id].vue           # Individual pet detail page
│   └── create.vue         # Create new pet post page
├── chats/
│   ├── index.vue           # Chat list page
│   └── [id].vue           # Individual chat conversation
├── adopt/
│   └── [id].vue           # Adoption request confirmation
└── auth/
    └── callback.vue       # OAuth callback handler
```

### 🧩 Frontend Components
**Location: `frontend/components/`**
- `SideNav.vue` - Side navigation bar (visible on all pages)
- `NavBar.vue` - (Not used, replaced by SideNav)

### 🎣 Frontend Composables (Reusable Logic)
**Location: `frontend/composables/`**
- `useAuth.js` - Authentication logic (login, register, logout)
- `useApi.js` - API request handler with authentication

### 🛡️ Frontend Middleware
**Location: `frontend/middleware/`**
- `auth.js` - Protects routes (requires login)
- `guest.js` - Redirects logged-in users (for login/register pages)
- `verified.js` - Requires verified account (for dashboard access)

### 🎨 Frontend Styling
**Location: `frontend/assets/css/main.css`**
- Global dark theme CSS variables
- Component styles
- Responsive design

### 📐 Frontend Layout
**Location: `frontend/layouts/default.vue`**
- Main layout wrapper
- Includes SideNav on all pages
- Content area for pages

---

## 🗄️ DATABASE

### Database Type: **SQLite**
- **File Location**: `backend/database/database.sqlite`
- **Why SQLite?**: 
  - No server setup needed
  - Perfect for development
  - Single file database
  - Easy to backup/restore

### Database Tables (Created via Migrations)

**Location: `backend/database/migrations/`**

1. **users** - User accounts
   - id, name, email, password, phone, address, avatar, verified, timestamps

2. **pets** - Pet listings
   - id, user_id, name, species, breed, age, gender, size, description, 
     medical_history, vaccinations, location, price, status, images, timestamps

3. **chats** - Chat conversations
   - id, user1_id, user2_id, pet_id, last_message_at, timestamps

4. **messages** - Chat messages
   - id, chat_id, sender_id, message, read_at, timestamps

5. **adoptions** - Adoption requests
   - id, pet_id, adopter_id, status, request_date, adoption_date, timestamps

6. **personal_access_tokens** - Laravel Sanctum tokens (for API auth)

### View Database
You can view/edit the database using:
- **DB Browser for SQLite** (free tool)
- **VS Code SQLite extension**
- **Command line**: `sqlite3 backend/database/database.sqlite`

---

## ⚙️ BACKEND STRUCTURE

### 🎯 Backend Framework: **Laravel 11** (PHP)

### 📁 Backend Directory Structure

```
backend/
├── app/
│   ├── Http/Controllers/Api/    # API Controllers
│   │   ├── AuthController.php    # Login/Register/Logout
│   │   ├── GoogleAuthController.php  # Google OAuth
│   │   ├── PetController.php     # Pet CRUD operations
│   │   ├── ChatController.php    # Chat management
│   │   ├── MessageController.php # Message handling
│   │   ├── AdoptionController.php # Adoption requests
│   │   └── VerificationController.php # Account verification
│   │
│   └── Models/                   # Database Models
│       ├── User.php              # User model
│       ├── Pet.php               # Pet model
│       ├── Chat.php              # Chat model
│       ├── Message.php           # Message model
│       └── Adoption.php          # Adoption model
│
├── routes/
│   └── api.php                   # API Routes (all endpoints)
│
├── database/
│   ├── migrations/               # Database table definitions
│   └── database.sqlite          # SQLite database file
│
├── config/
│   ├── database.php             # Database configuration
│   └── services.php             # Third-party services (Google OAuth)
│
└── .env                          # Environment variables (credentials)
```

### 🔌 API Endpoints

**Base URL**: `http://localhost:8000/api`

**Public Routes** (No authentication required):
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `GET /api/auth/google/url` - Get Google OAuth URL
- `GET /api/auth/google` - Redirect to Google
- `GET /api/auth/google/callback` - Google OAuth callback

**Protected Routes** (Requires authentication token):
- `POST /api/logout` - Logout
- `GET /api/user` - Get current user
- `GET /api/pets` - List all pets
- `POST /api/pets` - Create pet
- `GET /api/pets/{id}` - Get pet details
- `GET /api/chats` - Get user's chats
- `POST /api/messages` - Send message
- `POST /api/adoptions` - Create adoption request
- `POST /api/verify` - Verify account

---

## 🔐 REGISTRATION & LOGIN FLOW

### 📝 REGISTRATION PROCESS

#### Frontend Flow (`frontend/pages/register.vue`)

1. **User fills form**:
   ```javascript
   - Name
   - Email
   - Password
   - Confirm Password
   ```

2. **Form submission**:
   ```javascript
   // In register.vue
   const handleRegister = async () => {
     await auth.register(form.value)
     navigateTo('/verification')
   }
   ```

3. **Calls composable** (`frontend/composables/useAuth.js`):
   ```javascript
   const register = async (userData) => {
     const response = await apiFetch('/register', {
       method: 'POST',
       body: userData,
     })
     token.value = response.token  // Save token
     user.value = response.user    // Save user data
   }
   ```

4. **API request** (`frontend/composables/useApi.js`):
   ```javascript
   // Adds authentication headers
   // Makes request to: http://localhost:8000/api/register
   ```

#### Backend Flow (`backend/app/Http/Controllers/Api/AuthController.php`)

1. **Validation**:
   ```php
   $request->validate([
       'name' => 'required|string|max:255',
       'email' => 'required|email|unique:users',
       'password' => 'required|string|min:8|confirmed',
   ]);
   ```

2. **Create User**:
   ```php
   $user = User::create([
       'name' => $request->name,
       'email' => $request->email,
       'password' => Hash::make($request->password), // Encrypted
       'verified' => false, // New users need verification
   ]);
   ```

3. **Generate Token** (Laravel Sanctum):
   ```php
   $token = $user->createToken('auth_token')->plainTextToken;
   ```

4. **Return Response**:
   ```php
   return response()->json([
       'user' => $user,
       'token' => $token,
   ], 201);
   ```

5. **Database**: User saved to `users` table in SQLite

---

### 🔑 LOGIN PROCESS

#### Frontend Flow (`frontend/pages/login.vue`)

1. **User enters credentials**:
   ```javascript
   - Email
   - Password
   - Remember me (optional)
   ```

2. **Form submission**:
   ```javascript
   const handleLogin = async () => {
     await auth.login(email.value, password.value)
     navigateTo('/')  // Redirect to dashboard
   }
   ```

3. **Calls composable** (`frontend/composables/useAuth.js`):
   ```javascript
   const login = async (email, password) => {
     const response = await apiFetch('/login', {
       method: 'POST',
       body: { email, password },
     })
     token.value = response.token  // Save token to cookie
     user.value = response.user    // Save user to state
   }
   ```

#### Backend Flow (`backend/app/Http/Controllers/Api/AuthController.php`)

1. **Validation**:
   ```php
   $request->validate([
       'email' => 'required|email',
       'password' => 'required',
   ]);
   ```

2. **Find User**:
   ```php
   $user = User::where('email', $request->email)->first();
   ```

3. **Verify Password**:
   ```php
   if (!$user || !Hash::check($request->password, $user->password)) {
       throw ValidationException::withMessages([
           'email' => ['The provided credentials are incorrect.'],
       ]);
   }
   ```

4. **Generate Token**:
   ```php
   $token = $user->createToken('auth_token')->plainTextToken;
   ```

5. **Return Response**:
   ```php
   return response()->json([
       'user' => $user,
       'token' => $token,
   ]);
   ```

---

### 🔒 AUTHENTICATION SYSTEM

#### Token-Based Authentication (Laravel Sanctum)

1. **Token Storage**:
   - Frontend: Stored in cookie (`useCookie`)
   - Backend: Stored in `personal_access_tokens` table

2. **Token Usage**:
   - Every API request includes: `Authorization: Bearer {token}`
   - Backend validates token on protected routes

3. **Token Lifecycle**:
   - Created: On login/register
   - Valid: Until logout or expiration (7 days)
   - Deleted: On logout

#### Protected Routes

**Backend** (`backend/routes/api.php`):
```php
Route::middleware('auth:sanctum')->group(function () {
    // All routes here require valid token
});
```

**Frontend** (`frontend/middleware/auth.js`):
```javascript
// Redirects to /login if not authenticated
```

---

### 🌐 GOOGLE OAUTH LOGIN

#### Flow

1. **User clicks "Sign in with Google"**
2. **Frontend** calls `/api/auth/google/url`
3. **Backend** returns Google OAuth URL
4. **User redirected** to Google login
5. **Google redirects** to `/api/auth/google/callback`
6. **Backend** creates/logs in user
7. **Backend redirects** to frontend with token
8. **Frontend** (`auth/callback.vue`) saves token and logs in

**Controller**: `backend/app/Http/Controllers/Api/GoogleAuthController.php`

---

## 🔄 DATA FLOW EXAMPLE: Viewing Pets

1. **User visits** `/` (dashboard)
2. **Frontend** (`pages/index.vue`) loads
3. **Calls** `loadPets()` function
4. **Makes API request**: `GET /api/pets`
5. **Backend** (`PetController@index`) queries database
6. **Returns** JSON array of pets
7. **Frontend** displays pets in grid

---

## 📊 KEY TECHNOLOGIES

### Frontend
- **Nuxt.js 4** - Vue.js framework
- **Vue 3** - JavaScript framework
- **Composition API** - Modern Vue syntax
- **$fetch** - Built-in HTTP client

### Backend
- **Laravel 11** - PHP framework
- **Laravel Sanctum** - API authentication
- **Laravel Socialite** - OAuth integration
- **SQLite** - Database

### Authentication
- **Laravel Sanctum** - Token-based API auth
- **Google OAuth 2.0** - Social login

---

## 🛠️ HOW TO MODIFY

### Add a New Page
1. Create file in `frontend/pages/`
2. Example: `frontend/pages/about.vue`
3. Access at: `http://localhost:3000/about`

### Add a New API Endpoint
1. Add route in `backend/routes/api.php`
2. Create controller method in `backend/app/Http/Controllers/Api/`
3. Call from frontend using `useApi()` composable

### Modify Database
1. Create migration: `php artisan make:migration add_field_to_table`
2. Run migration: `php artisan migrate`
3. Update model in `backend/app/Models/`

---

## 📖 USEFUL COMMANDS

### Backend
```bash
cd backend
php artisan serve              # Start backend server
php artisan migrate            # Run database migrations
php artisan tinker             # Interactive PHP shell
php artisan route:list         # List all routes
```

### Frontend
```bash
cd frontend
npm run dev                    # Start development server
npm run build                  # Build for production
```

### Database
```bash
# View database
sqlite3 backend/database/database.sqlite

# SQL commands
.tables                        # List tables
SELECT * FROM users;           # View users
```

---

## 🎯 QUICK REFERENCE

| What | Where |
|------|-------|
| Frontend Pages | `frontend/pages/` |
| Frontend Components | `frontend/components/` |
| API Controllers | `backend/app/Http/Controllers/Api/` |
| Database File | `backend/database/database.sqlite` |
| API Routes | `backend/routes/api.php` |
| Models | `backend/app/Models/` |
| Migrations | `backend/database/migrations/` |
| Environment Config | `backend/.env` |

---

This is your complete application structure! 🎉



