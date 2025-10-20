<?php
class Bookmark {
    private $conn;
    private $table = "bookmarks";

    public function __construct($db){
        $this->conn = $db;
    }

    // Tambah bookmark
    public function tambah($user_id, $question_id){
        // Cek apakah sudah di-bookmark
        $queryCheck = "SELECT * FROM " . $this->table . " WHERE user_id=:user_id AND question_id=:question_id";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->bindParam(":user_id", $user_id);
        $stmtCheck->bindParam(":question_id", $question_id);
        $stmtCheck->execute();
        
        if($stmtCheck->rowCount() > 0){
            return false; // Sudah di-bookmark
        }
        
        $query = "INSERT INTO " . $this->table . " SET user_id=:user_id, question_id=:question_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":question_id", $question_id);
        return $stmt->execute();
    }

    // Hapus bookmark
    public function hapus($user_id, $question_id){
        $query = "DELETE FROM " . $this->table . " WHERE user_id=:user_id AND question_id=:question_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":question_id", $question_id);
        return $stmt->execute();
    }

    // Ambil semua bookmark user
    public function bacaPerUser($user_id){
        $query = "SELECT b.*, q.judul, q.isi, q.created_at, u.username 
                  FROM " . $this->table . " b 
                  JOIN questions q ON b.question_id = q.id 
                  JOIN users u ON q.user_id = u.id 
                  WHERE b.user_id=:user_id 
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cek apakah pertanyaan sudah di-bookmark
    public function sudahBookmark($user_id, $question_id){
        $query = "SELECT * FROM " . $this->table . " WHERE user_id=:user_id AND question_id=:question_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":question_id", $question_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
