# Movie Browser Application

A modern web application for browsing and managing favorite movies using the OMDB API. Built with Laravel 8 and PHP 8.

## ✨ Features

- ✅ **User Authentication** - Secure login system with session management
- ✅ **Movie Search** - Search movies by title, type, and year using OMDB API
- ✅ **Infinite Scroll** - Automatic loading of more movies as you scroll
- ✅ **Lazy Loading** - Images load progressively for better performance
- ✅ **Favorites Management** - Add and remove movies from your favorites collection
- ✅ **Movie Details** - View comprehensive information including ratings, cast, and plot
- ✅ **Multi-language** - Switch between English and Indonesian
- ✅ **Responsive Design** - Works perfectly on mobile, tablet, and desktop

## 🛠 Tech Stack

**Backend:**
- Laravel 8.x
- PHP 8.0+
- MySQL 5.7+

**Frontend:**
- Bootstrap 5.1.3
- jQuery 3.6.0
- Font Awesome 6.0
- Vanilla LazyLoad

**External API:**
- OMDB API (http://www.omdbapi.com/)

## 💻 Requirements

- PHP >= 8.0
- Composer >= 2.0
- MySQL >= 5.7 or 8.0
- Node.js >= 14.x
- NPM >= 6.x

**Required PHP Extensions:**
- OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo

## 📦 Installation

### 1. Extract Files

Extract the zip file to your desired location:
```bash
cd /path/to/your/webserver
unzip movie-browser.zip
cd movie-browser
```

### 2. Install Dependencies

**PHP Dependencies:**
```bash
composer install
```

**Node Dependencies:**
```bash
npm install
```

### 3. Environment Configuration

Copy environment file:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

Edit `.env` file and configure your settings:
```env
APP_NAME="Movie Browser"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=movie_browser
DB_USERNAME=root
DB_PASSWORD=

OMDB_API_KEY=your_api_key_here
```

### 4. Get OMDB API Key

1. Visit: http://www.omdbapi.com/apikey.aspx
2. Select **FREE** tier (1,000 requests/day)
3. Enter your email
4. Check your email for the API key
5. Add the key to your `.env` file

### 5. Setup Database

**Create database:**
```bash
mysql -u root -p
CREATE DATABASE movie_browser CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Or using phpMyAdmin:**
1. Open phpMyAdmin
2. Click "New" database
3. Name: `movie_browser`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

**Run migrations and seeders:**
```bash
php artisan migrate --seed
```

### 6. Setup Storage
```bash
php artisan storage:link
```

### 7. Compile Assets

**For development:**
```bash
npm run dev
```

**For production:**
```bash
npm run production
```

### 8. Run Application
```bash
php artisan serve
```

Visit: **http://localhost:8000**

### 9. Login

Use these credentials:
- **Username:** `tes`
- **Password:** `tes`

## 📁 Project Structure
```
movie-browser/
├── app/
│   ├── Http/Controllers/      # Application controllers
│   ├── Models/                # Database models
│   └── Services/              # OMDB API integration
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/               # Database seeders
├── resources/
│   ├── lang/                  # Language files (EN/ID)
│   └── views/                 # Blade templates
├── routes/
│   └── web.php               # Application routes
└── .env                      # Environment configuration
```

## 🔧 Configuration

### Database Setup (MySQL 8.0+)

If you encounter authentication errors with MySQL 8.0+:
```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';
FLUSH PRIVILEGES;
```

### File Permissions

**Linux/Mac:**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

**Or:**
```bash
sudo chown -R www-data:www-data storage boottrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🚀 Deployment to InfinityFree

### 1. Prepare Files

Build production assets:
```bash
npm run production
```

### 2. Upload Files

1. Login to InfinityFree cPanel
2. Open **File Manager**
3. Navigate to `htdocs` folder
4. Upload all project files except:
   - `node_modules/`
   - `.git/`
   - `storage/logs/*`

### 3. Setup Database

1. Go to **MySQL Databases** in cPanel
2. Create new database
3. Create database user
4. Add user to database with all privileges
5. Note: `database name`, `username`, `password`

### 4. Configure Environment

Edit `.env` file via File Manager or FTP:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://yourdomain.infinityfreeapp.com

DB_CONNECTION=mysql
DB_HOST=sqlxxx.infinityfreeapp.com
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

OMDB_API_KEY=your_api_key
```

### 5. Update .htaccess

Create/edit `.htaccess` in root directory:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### 6. Run Setup Commands

**Via cPanel Terminal or SSH:**
```bash
cd htdocs
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**If terminal not available**, import database manually:
1. Export local database: `mysqldump -u root -p movie_browser > database.sql`
2. Upload via phpMyAdmin
3. Import the SQL file

### 7. Set Permissions
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 🎯 Usage Guide

### Searching Movies

1. Go to **Movie List** page
2. Enter movie title (default: "movie")
3. **Optional:** Select type and year filters
4. Click **Search** or press Enter
5. Scroll down to load more results automatically

### Managing Favorites

**Add Favorite:**
- Click the heart icon ❤️ on any movie card
- Or click "Add to Favorites" on detail page

**Remove Favorite:**
- Click the filled heart icon ❤️ again
- Or remove from **My Favorites** page

**View Favorites:**
- Click **My Favorites** in navigation menu

### Changing Language

1. Click the globe icon 🌐 in navigation
2. Select **English** or **Indonesia**
3. All interface text will update

## 🐛 Troubleshooting

### Common Issues

**1. "Class not found" Error**
```bash
composer dump-autoload
```

**2. Database Connection Failed**
- Check `.env` database credentials
- Ensure database exists
- Verify MySQL is running

**3. Storage Permission Errors**
```bash
chmod -R 775 storage bootstrap/cache
```

**4. NPM/Node Errors**
```bash
rm -rf node_modules package-lock.json
npm cache clean --force
npm install
```

**5. OMDB API Not Working**
- Verify API key in `.env`
- Check daily limit (1000 requests/day)
- Test API: `curl "http://www.omdbapi.com/?apikey=YOUR_KEY&s=movie"`

**6. Images Not Loading**
- Check internet connection
- Verify LazyLoad JS is loaded
- Check browser console for errors

**7. Infinite Scroll Not Working**
- Clear browser cache
- Check JavaScript console
- Verify `#scroll-sentinel` element exists

## 📚 Libraries Used

| Library | Version | Purpose |
|---------|---------|---------|
| Laravel | 8.x | PHP Framework |
| Guzzle | 7.x | HTTP Client for API |
| Bootstrap | 5.1.3 | CSS Framework |
| jQuery | 3.6.0 | JavaScript Library |
| Font Awesome | 6.0.0 | Icons |
| Vanilla LazyLoad | 17.8.3 | Image Lazy Loading |

## 🏗 Architecture

**Design Pattern:** MVC + Service Layer

- **Controllers:** Handle HTTP requests and responses
- **Models:** Database interaction via Eloquent ORM
- **Views:** Blade templates for UI
- **Services:** Business logic (OMDB API integration)
- **Middleware:** Request filtering (authentication, locale)

## 🔒 Security Features

- ✅ CSRF Protection
- ✅ Password Hashing (Bcrypt)
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ XSS Protection (Blade escaping)
- ✅ Session-based Authentication
- ✅ Input Validation

## ⚡ Performance Features

- ✅ API Response Caching (1 hour)
- ✅ Image Lazy Loading
- ✅ Infinite Scroll Pagination
- ✅ Database Query Optimization
- ✅ Asset Minification

## 📄 License

This project is developed for technical test purposes.

---

**Built with Laravel 8 + PHP 8**

For questions or issues, please refer to the troubleshooting section above.

