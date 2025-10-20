<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Article.php";
require_once __DIR__ . "/../helpers/ResponseHelper.php";

class ArticleController {
    private $db;
    private $article;

    public function __construct(){
        $database = new Database();
        $this->db = $database->getConnection();
        $this->article = new Article($this->db);
    }

    private function cekAutentikasi(){
        $headers = getallheaders();
        if(empty($headers['User-Id'])){
            ResponseHelper::error("User tidak terautentikasi.", 401);
        }
        return $headers['User-Id'];
    }

    public function buatArtikel(){
        $user_id = $this->cekAutentikasi();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['judul']) || empty($data['isi'])){
            ResponseHelper::error("Judul dan isi artikel harus diisi.");
        }
        
        $id = $this->article->buat($user_id, $data['judul'], $data['isi']);
        
        if($id){
            ResponseHelper::json(["message" => "Artikel berhasil dibuat.", "id" => $id], 201);
        }
        ResponseHelper::error("Gagal membuat artikel.");
    }

    public function bacaArtikel(){
        $id = $_GET['id'] ?? null;
        
        if(!$id){
            $data = $this->article->bacaSemua();
            ResponseHelper::json($data);
        }
        
        $data = $this->article->baca($id);
        if($data){
            ResponseHelper::json($data);
        }
        ResponseHelper::error("Artikel tidak ditemukan.", 404);
    }

    public function updateArtikel(){
        $user_id = $this->cekAutentikasi();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['id']) || empty($data['judul']) || empty($data['isi'])){
            ResponseHelper::error("ID, judul, dan isi harus diisi.");
        }
        
        if($this->article->update($data['id'], $data['judul'], $data['isi'])){
            ResponseHelper::success("Artikel berhasil diperbarui.");
        }
        ResponseHelper::error("Gagal memperbarui artikel.");
    }

    public function hapusArtikel(){
        $user_id = $this->cekAutentikasi();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['id'])){
            ResponseHelper::error("ID artikel harus diisi.");
        }
        
        if($this->article->hapus($data['id'])){
            ResponseHelper::success("Artikel berhasil dihapus.");
        }
        ResponseHelper::error("Gagal menghapus artikel.");
    }
}

$action = $_GET['action'] ?? '';
$controller = new ArticleController();

switch($action){
    case "buat": $controller->buatArtikel(); break;
    case "baca": $controller->bacaArtikel(); break;
    case "update": $controller->updateArtikel(); break;
    case "hapus": $controller->hapusArtikel(); break;
    default: ResponseHelper::error("Aksi tidak dikenali.", 404);
}
?>
