<?php
class Answer {
    private $conn;
    private $table = "answers";

    public function __construct($db){
        $this->conn = $db;
    }

    public function buat($question_id, $user_id, $isi, $is_trusted = false){
        $query = "INSERT INTO " . $this->table . " 
                  SET question_id=:question_id, user_id=:user_id, isi=:isi, is_trusted=:is_trusted";
        $stmt = $this->conn->prepare($query);
        $trusted = $is_trusted ? 1 : 0;
        $stmt->bindParam(":question_id", $question_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":isi", $isi);
        $stmt->bindParam(":is_trusted", $trusted, PDO::PARAM_INT);
        
        if($stmt->execute()){
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function bacaPerPertanyaan($question_id){
        $query = "SELECT a.*, u.username, u.role 
                  FROM " . $this->table . " a 
                  JOIN users u ON a.user_id = u.id 
                  WHERE a.question_id=:question_id 
                  ORDER BY a.is_trusted DESC, a.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":question_id", $question_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $user_id, $isi){
        $query = "UPDATE " . $this->table . " SET isi=:isi WHERE id=:id AND user_id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":isi", $isi);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    public function hapus($id, $user_id){
        $query = "DELETE FROM " . $this->table . " WHERE id=:id AND user_id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }
}
?>
