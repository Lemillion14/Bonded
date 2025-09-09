<?php
require_once 'functions.php';

class Auth {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function register($userData) {
        $query = "INSERT INTO users SET username=:username, email=:email, password=:password, 
                 first_name=:first_name, last_name=:last_name, age=:age, gender=:gender";
        
        $stmt = $this->conn->prepare($query);
        
        $password_hash = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':username', $userData['username']);
        $stmt->bindParam(':email', $userData['email']);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':first_name', $userData['first_name']);
        $stmt->bindParam(':last_name', $userData['last_name']);
        $stmt->bindParam(':age', $userData['age']);
        $stmt->bindParam(':gender', $userData['gender']);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    public function login($username, $password) {
        $query = "SELECT id, username, password FROM users WHERE username=:username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                return true;
            }
        }
        return false;
    }
}
?>