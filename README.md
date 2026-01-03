# Star-Clicks Clone

A complete clone of the Star-Clicks advertising platform built with PHP, MySQL, and TailwindCSS.

## Features

- User registration and authentication (Publishers and Advertisers)
- Ad management system
- Click tracking and payment processing
- Withdrawal system
- Referral program
- Admin dashboard
- Responsive design with TailwindCSS

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache or Nginx web server
- XAMPP (recommended for local development)

## Installation

1. Clone this repository to your web server directory:
   ```bash
   git clone <repository-url> c:/xampp/htdocs/ptc
   ```

2. Create a MySQL database:
   ```sql
   CREATE DATABASE star_clicks_clone;
   ```

3. Update database credentials in `includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Update if needed
   define('DB_NAME', 'star_clicks_clone');
   ```

4. Run the database initialization script:
   ```bash
   php init_database.php
   ```

5. Access the application via your web browser:
   ```
   http://localhost/ptc
   ```

## Default Admin Account

After running the initialization script, a default admin account is created:
- Email: `admin@star-clicks-clone.com`
- Password: `admin123`

## Project Structure

```
ptc/
├── admin/                 # Admin panel
├── api/                   # API endpoints
├── css/                   # CSS files
├── images/               # Image assets
├── includes/             # PHP includes (config, functions)
├── js/                   # JavaScript files
├── portal/               # User portal (dashboard, profile, etc.)
├── database_schema.sql   # Database schema
├── init_database.php     # Database initialization script
├── index.php            # Main landing page
└── ...
```

## Security Features

- Password hashing with PHP's password_hash()
- CSRF protection
- Input sanitization
- Session management
- SQL injection prevention with prepared statements
- CAPTCHA for registration/login

## Customization

The platform can be customized by:
- Updating the site settings in the `site_settings` database table
- Modifying the TailwindCSS configurations
- Adding new payment methods
- Adjusting commission rates

## License

This project is for educational purposes only. The original Star-Clicks brand and trademarks belong to their respective owners.