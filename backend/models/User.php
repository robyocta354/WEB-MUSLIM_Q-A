<?php
class User {
    private $conn;
    private $table = "users";

    public $id;
    public $username;
    public $password;
    public $role;

    public function __construct($db){
        $this->conn = $db;
    }

    public function register($username, $password, $role = "user"){
        $query = "INSERT INTO " . $this->table . " SET username=:username, password=:password, role=:role";
        $stmt = $this->conn->prepare($query);
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashedPass);
        $stmt->bindParam(":role", $role);
        
        try {
            if($stmt->execute()){
                return true;
            }
        } catch(PDOException $e) {
            return false;
        }
        return false;
    }

    public function login($username, $password){
        $query = "SELECT * FROM " . $this->table . " WHERE username=:username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user && password_verify($password, $user['password'])){
            unset($user['password']);
            return $user;
        }
        return false;
    }

    public function getById($id){
        $query = "SELECT id, username, role FROM " . $this->table . " WHERE id=:id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
