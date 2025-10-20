<?php
class Like {
    private $conn;
    private $table = "likes";

    public function __construct($db){
        $this->conn = $db;
    }

    public function tambahLike($answer_id, $user_id){
        $queryCheck = "SELECT * FROM " . $this->table . " WHERE answer_id=:answer_id AND user_id=:user_id";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->bindParam(":answer_id", $answer_id);
        $stmtCheck->bindParam(":user_id", $user_id);
        $stmtCheck->execute();
        
        if($stmtCheck->rowCount() > 0){
            return false;
        }
        
        $query = "INSERT INTO " . $this->table . " SET answer_id=:answer_id, user_id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":answer_id", $answer_id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    public function jumlahLike($answer_id){
        $query = "SELECT COUNT(*) AS total FROM " . $this->table . " WHERE answer_id=:answer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":answer_id", $answer_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['total'] : 0;
    }

    public function sudahLike($answer_id, $user_id){
        $query = "SELECT * FROM " . $this->table . " WHERE answer_id=:answer_id AND user_id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":answer_id", $answer_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
