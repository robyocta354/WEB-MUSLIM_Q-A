<?php
class Database {
    private $host = "localhost";
    private $db_name = "muslimqa";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection(){
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception){
            http_response_code(500);
            echo json_encode([
                "error" => "Koneksi database gagal: " . $exception->getMessage()
            ]);
            exit;
        }
        return $this->conn;
    }
}
?>
