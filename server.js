const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const mysql = require('mysql2/promise');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(express.static('.'));

// Database connection pool
const pool = mysql.createPool({
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASS || '',
  database: 'contact_db',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

// Validate input
function validateInput(name, email, subject, message) {
  const errors = [];

  if (!name || name.trim().length === 0 || name.length > 100) {
    errors.push('Name must be 1-100 characters');
  }

  if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
    errors.push('Valid email is required');
  }

  if (email.length > 255) {
    errors.push('Email too long');
  }

  if (subject && subject.length > 200) {
    errors.push('Subject must be under 200 characters');
  }

  if (!message || message.trim().length === 0) {
    errors.push('Message is required');
  }

  return errors;
}

// Contact form endpoint
app.post('/api/process-contact.php', async (req, res) => {
  try {
    const { name, email, subject, message } = req.body;

    // Validate input
    const errors = validateInput(name, email, subject, message);
    if (errors.length > 0) {
      return res.status(400).json({
        success: false,
        message: errors.join('; ')
      });
    }

    // Get connection from pool
    const connection = await pool.getConnection();

    try {
      // Insert into database
      const result = await connection.execute(
        'INSERT INTO contacts (name, email, subject, message, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
        [name.trim(), email.trim(), subject?.trim() || null, message.trim(), 'new']
      );

      res.status(200).json({
        success: true,
        message: 'Message received successfully. We will review and respond shortly.',
        id: result[0].insertId
      });
    } finally {
      connection.release();
    }
  } catch (error) {
    console.error('Contact form error:', error.message);
    console.error('Error code:', error.code);

    // Check if it's a database connection error
    if (error.code === 'PROTOCOL_CONNECTION_LOST' || error.code === 'ER_ACCESS_DENIED_ERROR' || error.code === 'ETIMEDOUT' || error.code === 'ECONNREFUSED') {
      console.error('Database connection issue. Check:');
      console.error(`- DB_HOST: ${process.env.DB_HOST}`);
      console.error(`- DB_USER: ${process.env.DB_USER}`);
      console.error(`- Is MySQL running?`);
      
      return res.status(503).json({
        success: false,
        message: `Database connection failed: ${error.code}. Ensure MySQL is running at ${process.env.DB_HOST}:3306`
      });
    }

    res.status(500).json({
      success: false,
      message: `Error: ${error.message}`
    });
  }
});

// Health check endpoint
app.get('/api/health', (req, res) => {
  res.json({ status: 'ok' });
});

app.listen(PORT, () => {
  console.log(`🚀 Server running at http://localhost:${PORT}`);
  console.log(`📧 Contact form endpoint: http://localhost:${PORT}/api/process-contact.php`);
});

process.on('SIGINT', async () => {
  await pool.end();
  process.exit(0);
});
