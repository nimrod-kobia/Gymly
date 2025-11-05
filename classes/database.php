<?php

class Database {
    private static ?PDO $sharedConnection = null;
    private static bool $attemptedConnection = false;

    private $db_type;
    private $db_host;
    private $db_user;
    private $db_pass;
    private $db_name;
    private $db_port;
    private $db_ssl_mode;
    private $db_timeout;

    public function __construct() {
        $this->db_type = DB_TYPE;
        $this->db_host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->db_user = DB_USER;
        $this->db_pass = DB_PASS;
        $this->db_port = DB_PORT;
        $this->db_ssl_mode = DB_SSL_MODE;
        $this->db_timeout = DB_CONN_TIMEOUT > 0 ? DB_CONN_TIMEOUT : 5;
    }

    public function connect() {
        if (self::$sharedConnection instanceof PDO) {
            return self::$sharedConnection;
        }

        if (self::$attemptedConnection) {
            return null;
        }

        self::$attemptedConnection = true;

        $hosts = [];

        // Prefer the pooled host first if one is configured to reduce connection churn.
        if (defined('DB_POOL_HOST') && DB_POOL_HOST) {
            $hosts[] = DB_POOL_HOST;
        }

        // Always attempt the primary host as well.
        $hosts[] = $this->db_host;

        foreach ($hosts as $host) {
            if (!$host) {
                continue;
            }

            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;sslmode=%s',
                $this->db_type,
                $host,
                $this->db_port,
                $this->db_name,
                $this->db_ssl_mode
            );

            try {
                $pdo = new PDO($dsn, $this->db_user, $this->db_pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => $this->db_timeout,
                ]);

                self::$sharedConnection = $pdo;
                return self::$sharedConnection;
            } catch (PDOException $e) {
                error_log(sprintf('Connection failed for host %s: %s', $host, $e->getMessage()));
            }
        }

        return null;
    }
}
?>
