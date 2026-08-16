<?php
/**
 * Database Configuration and Connection
 * Provides a reusable MySQL connection for the API
 */

class Database {
    private $conn;
    private $host;
    private $user;
    private $password;
    private $database;

    public function __construct() {
        // Load environment variables or use defaults
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
        $this->database = getenv('DB_NAME') ?: 'contact_db';
    }

    /**
     * Get database connection
     * @return mysqli
     * @throws Exception
     */
    public function connect() {
        if ($this->conn === null) {
            // Create connection
            $this->conn = new mysqli(
                $this->host,
                $this->user,
                $this->password,
                $this->database
            );

            // Check connection
            if ($this->conn->connect_error) {
                throw new Exception('Database connection failed: ' . $this->conn->connect_error);
            }

            // Set charset to UTF-8
            $this->conn->set_charset('utf8mb4');
        }

        return $this->conn;
    }

    /**
     * Close database connection
     */
    public function disconnect() {
        if ($this->conn !== null) {
            $this->conn->close();
            $this->conn = null;
        }
    }

    /**
     * Execute a prepared statement
     * @param string $query SQL query with placeholders (?)
     * @param array $params Parameters to bind
     * @param string $types Parameter types (s=string, i=integer, d=double, b=blob)
     * @return mixed Result object or affected rows
     * @throws Exception
     */
    public function executeQuery($query, $params = [], $types = '') {
        $conn = $this->connect();
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception('Statement preparation failed: ' . $conn->error);
        }

        // Bind parameters if provided
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // Execute statement
        if (!$stmt->execute()) {
            throw new Exception('Query execution failed: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    /**
     * Execute insert/update/delete query
     * @param string $query SQL query with placeholders (?)
     * @param array $params Parameters to bind
     * @param string $types Parameter types (s=string, i=integer, d=double, b=blob)
     * @return int Number of affected rows
     * @throws Exception
     */
    public function executeUpdate($query, $params = [], $types = '') {
        $conn = $this->connect();
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception('Statement preparation failed: ' . $conn->error);
        }

        // Bind parameters if provided
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // Execute statement
        if (!$stmt->execute()) {
            throw new Exception('Query execution failed: ' . $stmt->error);
        }

        $affected_rows = $stmt->affected_rows;
        $stmt->close();

        return $affected_rows;
    }

    /**
     * Get the last inserted ID
     * @return int Last insert ID
     */
    public function getLastInsertId() {
        return $this->conn->insert_id;
    }

    /**
     * Check if database connection is active
     * @return bool
     */
    public function isConnected() {
        return $this->conn !== null && $this->conn->ping();
    }
}

// Create and return database instance
return new Database();
