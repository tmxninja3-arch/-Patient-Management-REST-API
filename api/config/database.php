<?php

class Database {
    private $host = "localhost";
    private $db_name = "hospital2_db";
    private $username = "root";
    private $password = "";
    public $conn;
    
   //Flow: Create → Configure → Connect → Return 

    public function getConnection() {
        $this->conn = null;   
        try {

            $this->conn = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->db_name
            );
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            // Set character encoding
            $this->conn->set_charset("utf8");
            
        } catch (Exception $e) {
            echo "Database Error: " . $e->getMessage();
            return null;
        }
        
        return $this->conn;
    }
}
?>