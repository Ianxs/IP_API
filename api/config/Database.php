<?php

require __DIR__ . '../../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class Database {
    private static $instance = null;
    private $connection;
    private $host;
    private $databaseName;
    private $userName;
    private $password;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'];
        $this->databaseName = $_ENV['DB_NAME'];
        $this->userName = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];

        if (!isset($_ENV['DB_HOST'])) {
            die("❌ ERROR: Environment variables not loaded!");
        }

        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};port=3307;dbname={$this->databaseName}",
                $this->userName,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $this->connection->exec("set names utf8");
        } catch (PDOException $exception) {
            error_log("Database Connection Error: " . $exception->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
