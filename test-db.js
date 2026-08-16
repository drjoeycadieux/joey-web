// Test database connection
const mysql = require('mysql2/promise');
require('dotenv').config();

async function testConnection() {
  try {
    console.log('🔍 Testing MySQL connection...');
    console.log(`Host: ${process.env.DB_HOST}`);
    console.log(`User: ${process.env.DB_USER}`);
    console.log(`Database: ${process.env.DB_NAME}`);

    const connection = await mysql.createConnection({
      host: process.env.DB_HOST,
      user: process.env.DB_USER,
      password: process.env.DB_PASS,
      database: process.env.DB_NAME,
      waitForConnections: true,
      connectionLimit: 10,
      queueLimit: 0
    });

    const result = await connection.execute('SELECT 1');
    console.log('✅ MySQL connection successful!');
    console.log('Query result:', result);
    
    await connection.end();
    process.exit(0);
  } catch (error) {
    console.error('❌ MySQL connection failed:');
    console.error(error.message);
    console.error('Code:', error.code);
    process.exit(1);
  }
}

testConnection();
