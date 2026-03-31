<?php
class Database {
    private $host = "107.180.113.173";
    private $db_name = "ssbseducation";
    private $username = "srikanth";
    private $password = "9885@Sreekanth";

    public function connect() {
        try {
            $conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;

        } catch (PDOException $e) {
            die("DB Connection failed: " . $e->getMessage());
        }
    }
}