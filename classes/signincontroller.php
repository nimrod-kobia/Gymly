<?php
class signInController {
    private $usernameOrEmail;
    private $password;
    private $errors = [];
    private $userData;

    public function __construct($usernameOrEmail, $password) {
        $this->usernameOrEmail = trim($usernameOrEmail);
        $this->password = $password;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getUserData() {
        return $this->userData;
    }

    public function validateInputs() {
        if (empty($this->usernameOrEmail)) {
            $this->errors['usernameOrEmail'] = "Username or Email is required.";
        }
        if (empty($this->password)) {
            $this->errors['password'] = "Password is required.";
        }
        return empty($this->errors);
    }

    public function authenticateUser() {
        try {
            $database = new Database();
            $pdo = $database->connect();

            if ($pdo) {
                // allow login by either username or email
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :ue OR username = :ue LIMIT 1");
                $stmt->execute([':ue' => $this->usernameOrEmail]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($this->password, $user['password_hash'])) {
                    $this->userData = $user;
                    return true;
                } else {
                    $this->errors['auth'] = "Invalid username/email or password.";
                }
            }
        } catch (PDOException $e) {
            error_log("Database error in authenticateUser: " . $e->getMessage());
            $this->errors['database'] = "System error. Please try again later.";
        }
        return false;
    }
}
