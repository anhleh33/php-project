<?php
require_once __DIR__ . '/../vendor/autoload.php';// Tải MongoDB library

use MongoDB\Client;

class Database {
    private static $instance = null;
    private $client;
    private $db;

    private function __construct() {
        $uri = "mongodb+srv://anhlehoang2004:anhlehoang2004@cluster0.rkobwwr.mongodb.net/quizapp?retryWrites=true&w=majority&appName=Cluster0";

        try {
            $this->client = new Client($uri);
            $this->db = $this->client->selectDatabase("quiz_system"); // Thay đổi tên DB nếu muốn
        } catch (Exception $e) {
            die("Không thể kết nối MongoDB: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getDB() {
        return $this->db;
    }   
}
