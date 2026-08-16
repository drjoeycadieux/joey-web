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

## Configuration

The PHP backend (`api/process-contact.php`) reads database credentials from environment variables:

### Environment Variables

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

### For Local Testing

If using PHP locally:
```bash
php -S localhost:8000
```

Then visit `http://localhost:8000/contact.html`

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
4. **api/process-contact.php** - Backend handler for database insertion

## Hosting Considerations

- Ensure PHP is enabled on your web server
- MySQL/MariaDB database must be accessible
- Use HTTPS in production for form security
- Implement CSRF protection for additional security
- Consider adding rate limiting to prevent spam

## Error Handling

- Form displays user-friendly error messages
- Backend logs detailed errors for debugging
- All errors return proper HTTP status codes
- Database errors don't expose sensitive information to users
