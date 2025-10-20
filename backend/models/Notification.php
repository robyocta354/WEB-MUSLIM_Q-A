<?php
class Notification {
    private $conn;
    private $table = "notifications";

    public function __construct($db){
        $this->conn = $db;
    }

    // Buat notifikasi baru
    public function buat($user_id, $question_id, $answer_id, $message){
        $query = "INSERT INTO " . $this->table . " 
                  SET user_id=:user_id, question_id=:question_id, answer_id=:answer_id, message=:message";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":question_id", $question_id);
        $stmt->bindParam(":answer_id", $answer_id);
        $stmt->bindParam(":message", $message);
        return $stmt->execute();
    }

    // Ambil notifikasi user (belum dibaca)
    public function bacaPerUser($user_id){
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE user_id=:user_id 
                  ORDER BY created_at DESC LIMIT 20";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Jumlah notifikasi belum dibaca
    public function jumlahBelumDibaca($user_id){
        $query = "SELECT COUNT(*) AS total FROM " . $this->table . " 
                  WHERE user_id=:user_id AND is_read=0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['total'] : 0;
    }

    // Tandai notifikasi sudah dibaca
    public function tandaiDibaca($id){
        $query = "UPDATE " . $this->table . " SET is_read=1 WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Tandai semua notifikasi user sudah dibaca
    public function tandaiSemuaDibaca($user_id){
        $query = "UPDATE " . $this->table . " SET is_read=1 WHERE user_id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }
}
?>
