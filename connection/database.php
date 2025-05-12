<?php
class Database
{
    private $host = 'localhost'; // Usar localhost para conexión local
    private $db_name = 'bcs_floragames'; // El nombre de base de datos local
    private $db_user = 'root'; // Usuario predeterminado de MySQL
    private $db_password = '';
    private $charset = 'utf8mb4';

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
