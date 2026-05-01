<?php
class Database
{
    private $host;
    private $db_name;
    private $db_user;
    private $db_password;
    private $charset = 'utf8mb4';

    public function __construct()
    {
        $this->host      = getenv('DB_HOST')     ?: 'mysql';
        $this->db_name   = getenv('DB_NAME')     ?: 'bcs_floragames';
        $this->db_user   = getenv('DB_USER')     ?: 'floragames_user';
        $this->db_password = getenv('DB_PASS')   ?: 'floragames_pass';
    }

    public function getConnection()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};port=3306;charset={$this->charset}";
        try {
            $pdo = new PDO($dsn, $this->db_user, $this->db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error al intentar la conexión a la base de datos: " . $e->getMessage());
        }
    }
}