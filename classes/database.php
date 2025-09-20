<?php

class Database {
    private $db_type;
    private $db_host;
    private $db_user;
    private $db_pass;
    private $db_name;
    private $db_port;
    private $db_ssl_mode;

    public function __construct() {
        $this->db_type = DB_TYPE;
        $this->db_host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->db_user = DB_USER;
        $this->db_pass = DB_PASS;
        $this->db_port = DB_PORT;
        $this->db_ssl_mode = DB_SSL_MODE;
    }

    public function connect() {
        $dbData = "$this->db_type:host=$this->db_host;port=$this->db_port;dbname=$this->db_name;sslmode=$this->db_ssl_mode";

        try {
            $db = new PDO($dbData, $this->db_user, $this->db_pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            return $db;
        } catch (PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            return null;
        }
    }    
}
?>