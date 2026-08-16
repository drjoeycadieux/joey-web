<?php
/**
 * MySQL Database Connection
 */

class Database
{
    private ?mysqli $conn = null;

    private string $host;
    private string $user;
    private string $password;
    private string $database;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
        $this->database = getenv('DB_NAME') ?: 'contact_db';
    }

    public function connect(): mysqli
    {
        if ($this->conn === null) {

            mysqli_report(MYSQLI_REPORT_OFF);

            $this->conn = new mysqli(
                $this->host,
                $this->user,
                $this->password,
                $this->database
            );

            if ($this->conn->connect_errno) {
                throw new Exception(
                    'Database connection failed: ' .
                    $this->conn->connect_error
                );
            }

            if (!$this->conn->set_charset('utf8mb4')) {
                throw new Exception('Failed to set database charset');
            }
        }

        return $this->conn;
    }

    public function executeUpdate(
        string $query,
        array $params = [],
        string $types = ''
    ): int {
        $conn = $this->connect();

        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception(
                'Statement preparation failed: ' . $conn->error
            );
        }

        if (!empty($params)) {
            if ($types === '' || strlen($types) !== count($params)) {
                $stmt->close();
                throw new Exception('Invalid parameter types');
            }

            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();

            throw new Exception(
                'Query execution failed: ' . $error
            );
        }

        $affectedRows = $stmt->affected_rows;

        $stmt->close();

        return $affectedRows;
    }

    public function disconnect(): void
    {
        if ($this->conn !== null) {
            $this->conn->close();
            $this->conn = null;
        }
    }
}

return new Database();