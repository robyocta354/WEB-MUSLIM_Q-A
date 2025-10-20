<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Bookmark.php";
require_once __DIR__ . "/../helpers/ResponseHelper.php";

class BookmarkController {
    private $db;
    private $bookmark;

    public function __construct(){
        $database = new Database();
        $this->db = $database->getConnection();
        $this->bookmark = new Bookmark($this->db);
    }

    private function cekAutentikasi(){
        $headers = getallheaders();
        if(empty($headers['User-Id'])){
            ResponseHelper::error("User tidak terautentikasi.", 401);
        }
        return $headers['User-Id'];
    }

    // Tambah bookmark
    public function tambahBookmark(){
        $user_id = $this->cekAutentikasi();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['question_id'])){
            ResponseHelper::error("ID pertanyaan harus diisi.");
        }
        
        $success = $this->bookmark->tambah($user_id, $data['question_id']);
        
        if($success){
            ResponseHelper::json(["message" => "Pertanyaan berhasil disimpan."]);
        }
        ResponseHelper::error("Pertanyaan sudah ada di bookmark.");
    }

    // Hapus bookmark
    public function hapusBookmark(){
        $user_id = $this->cekAutentikasi();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['question_id'])){
            ResponseHelper::error("ID pertanyaan harus diisi.");
        }
        
        if($this->bookmark->hapus($user_id, $data['question_id'])){
            ResponseHelper::json(["message" => "Bookmark berhasil dihapus."]);
        }
        ResponseHelper::error("Gagal menghapus bookmark.");
    }

    // Ambil semua bookmark user
    public function bacaBookmark(){
        $user_id = $this->cekAutentikasi();
        $data = $this->bookmark->bacaPerUser($user_id);
        ResponseHelper::json($data);
    }

    // Cek status bookmark
    public function cekBookmark(){
        $user_id = $this->cekAutentikasi();
        $question_id = $_GET['question_id'] ?? null;
        
        if(!$question_id){
            ResponseHelper::error("ID pertanyaan harus disertakan.");
        }
        
        $sudah = $this->bookmark->sudahBookmark($user_id, $question_id);
        ResponseHelper::json(["bookmarked" => $sudah]);
    }
}

$action = $_GET['action'] ?? '';
$controller = new BookmarkController();

switch($action){
    case "tambah": $controller->tambahBookmark(); break;
    case "hapus": $controller->hapusBookmark(); break;
    case "baca": $controller->bacaBookmark(); break;
    case "cek": $controller->cekBookmark(); break;
    default: ResponseHelper::error("Aksi tidak dikenali.", 404);
}
?>
