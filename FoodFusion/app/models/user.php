<?php
    class User {
        private $conn;
        private $table_name = "users";

        public function __construct($db) {
            $this->conn = $db;
        }

        public function create($first_name, $last_name, $username, $password, $email) {
            $sql = 'INSERT INTO users (first_name, last_name, username, password, email) VALUES (?, ?, ?, ?, ?)';
            $stmt = $this->conn->prepare($sql);
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bind_param("sssss", $first_name, $last_name, $username, $hashed_password, $email);
            return $stmt->execute();
    

            
        }

        public function getAll() {
            $sql = "SELECT * FROM " . $this->table_name;
            $result = $this->conn->query($sql);
            $users = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $users[] = $row;
                }
            }
            
            return $users;
        }

    }
