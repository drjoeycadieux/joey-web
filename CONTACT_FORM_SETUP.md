# Contact Form Setup Guide

## Database Setup

Run the following SQL to create your database and table:

```sql
CREATE DATABASE IF NOT EXISTS contact_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE contact_db;

CREATE TABLE contacts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(200) DEFAULT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'archived') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;
```

## Local Development Setup (Node.js)

### 1. Install Dependencies
```bash
npm install
```

### 2. Configure Environment
Copy `.env.example` to `.env` and update with your database credentials:
```bash
cp .env.example .env
```

Edit `.env`:
```
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
PORT=3000
```

### 3. Start the Server
```bash
npm start
```

Or for development with auto-reload:
```bash
npm run dev
```

Then visit: `http://localhost:3000/contact.html`

---

## Production Setup (PHP)

### Configuration

The PHP backend (`api/process-contact.php`) reads database credentials from environment variables:

#### Environment Variables

Set these in your hosting environment or `.env` file:

```
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
```

If not set, defaults are:
- `DB_HOST`: localhost
- `DB_USER`: root
- `DB_PASS`: (empty)

### Server Requirements
- PHP 7.2+
- MySQL 5.7+ or MariaDB 10.2+
- Web server (Apache/Nginx) with PHP enabled

---

## Form Features

✅ **Frontend Validation**
- Required field checking
- Email format validation
- Character limits enforcement

✅ **Backend Validation**
- SQL injection prevention (prepared statements)
- Input sanitization
- Type checking

✅ **Database Fields**
- `name`: VARCHAR(100) - Required
- `email`: VARCHAR(255) - Required, validated
- `subject`: VARCHAR(200) - Optional
- `message`: TEXT - Required
- `status`: Defaults to 'new'
- `created_at` & `updated_at`: Automatic timestamps

## Files Modified

1. **contact.html** - Added contact form with fields matching the database schema
2. **assets/js/contact.js** - Added form submission handler with validation
3. **assets/css/index.css** - Added form styling matching the site design
4. **api/process-contact.php** - Backend handler for database insertion (PHP version)
5. **server.js** - Node.js Express backend (for development)
6. **package.json** - Dependencies and scripts
7. **.env.example** - Environment configuration template

## Troubleshooting

### Database Connection Error
- Ensure MySQL is running
- Check credentials in `.env`
- Verify `contact_db` database exists

### 404 Error on Form Submission
- Make sure server is running (`npm start`)
- Check that server is on correct port (default: 3000)
- Browser console should show the API URL being called

### Form Not Validating
- Check browser console for JavaScript errors
- Ensure all fields meet requirements (name, email, message)
- Email must be in valid format

## Frontend to Backend Communication

The form sends JSON to `/api/process-contact.php`:

**Request:**
```json
{
  "name": "Your Name",
  "email": "your@email.com",
  "subject": "Optional subject",
  "message": "Your message"
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Message received successfully...",
  "id": 1
}
```

**Response (Error):**
```json
{
  "success": false,
  "message": "Error description"
}
```
