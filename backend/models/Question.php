<?php
class Question {
    private $conn;
    private $table = "questions";

    public function __construct($db){
        $this->conn = $db;
    }

    public function buat($user_id, $judul, $isi){
        $query = "INSERT INTO " . $this->table . " SET user_id=:user_id, judul=:judul, isi=:isi";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":judul", $judul);
        $stmt->bindParam(":isi", $isi);
        
        if($stmt->execute()){
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function bacaSemua(){
        $query = "SELECT q.*, u.username 
                  FROM " . $this->table . " q 
                  JOIN users u ON q.user_id = u.id 
                  ORDER BY q.created_at DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function baca($id){
        $query = "SELECT q.*, u.username 
                  FROM " . $this->table . " q 
                  JOIN users u ON q.user_id = u.id 
                  WHERE q.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $judul, $isi){
        $query = "UPDATE " . $this->table . " SET judul=:judul, isi=:isi WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":judul", $judul);
        $stmt->bindParam(":isi", $isi);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function hapus($id){
        $query = "DELETE FROM " . $this->table . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
