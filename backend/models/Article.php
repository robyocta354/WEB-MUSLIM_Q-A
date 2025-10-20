<?php
class Article {
    private $conn;
    private $table = "articles";

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
        $query = "SELECT a.*, u.username 
                  FROM " . $this->table . " a 
                  JOIN users u ON a.user_id = u.id 
                  ORDER BY a.created_at DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function baca($id){
        $query = "SELECT a.*, u.username 
                  FROM " . $this->table . " a 
                  JOIN users u ON a.user_id = u.id 
                  WHERE a.id = :id LIMIT 1";
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
