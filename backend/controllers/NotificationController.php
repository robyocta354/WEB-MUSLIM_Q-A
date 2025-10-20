<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Notification.php";
require_once __DIR__ . "/../helpers/ResponseHelper.php";

class NotificationController {
    private $db;
    private $notification;

    public function __construct(){
        $database = new Database();
        $this->db = $database->getConnection();
        $this->notification = new Notification($this->db);
    }

    private function cekAutentikasi(){
        $headers = getallheaders();
        if(empty($headers['User-Id'])){
            ResponseHelper::error("User tidak terautentikasi.", 401);
        }
        return $headers['User-Id'];
    }

    // Ambil notifikasi user
    public function bacaNotifikasi(){
        $user_id = $this->cekAutentikasi();
        $data = $this->notification->bacaPerUser($user_id);
        ResponseHelper::json($data);
    }

    // Jumlah notifikasi belum dibaca
    public function jumlahBelumDibaca(){
        $user_id = $this->cekAutentikasi();
        $jumlah = $this->notification->jumlahBelumDibaca($user_id);
        ResponseHelper::json(["unread_count" => $jumlah]);
    }

    // Tandai notifikasi sudah dibaca
    public function tandaiDibaca(){
        $user_id = $this->cekAutentikasi();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['id'])){
            ResponseHelper::error("ID notifikasi harus diisi.");
        }
        
        if($this->notification->tandaiDibaca($data['id'])){
            ResponseHelper::json(["message" => "Notifikasi ditandai sudah dibaca."]);
        }
        ResponseHelper::error("Gagal menandai notifikasi.");
    }

    // Tandai semua notifikasi sudah dibaca
    public function tandaiSemuaDibaca(){
        $user_id = $this->cekAutentikasi();
        
        if($this->notification->tandaiSemuaDibaca($user_id)){
            ResponseHelper::json(["message" => "Semua notifikasi ditandai sudah dibaca."]);
        }
        ResponseHelper::error("Gagal menandai notifikasi.");
    }
}

$action = $_GET['action'] ?? '';
$controller = new NotificationController();

switch($action){
    case "baca": $controller->bacaNotifikasi(); break;
    case "jumlah": $controller->jumlahBelumDibaca(); break;
    case "tandai": $controller->tandaiDibaca(); break;
    case "tandai_semua": $controller->tandaiSemuaDibaca(); break;
    default: ResponseHelper::error("Aksi tidak dikenali.", 404);
}
?>
