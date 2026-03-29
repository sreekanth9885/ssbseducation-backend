<?php
class Database {
    private $host = "107.180.113.173";
    private $db_name = "ssbseducation";
    private $username = "srikanth";
    private $password = "9885@Sreekanth";

    public function connect() {
        $conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->db_name
        );

        if ($conn->connect_error) {
            die("DB Connection failed");
        }

        return $conn;
    }
}