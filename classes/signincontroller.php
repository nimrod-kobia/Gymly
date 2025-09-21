<?php
class SignInController {
    private string $usernameOrEmail;
    private string $password;
    private array $errors = [];
    private ?array $userData = null;

    public function __construct(string $usernameOrEmail, string $password) {
        $this->usernameOrEmail = trim($usernameOrEmail);
        $this->password = $password;
    }

    public function validateInputs(): bool {
        if (empty($this->usernameOrEmail)) {
            $this->errors['usernameOrEmail'] = "Username or Email is required.";
        }
        if (empty($this->password)) {
            $this->errors['password'] = "Password is required.";
        }
        return empty($this->errors);
    }

    public function authenticateUser(): bool {
        try {
            $database = new Database();
            $pdo = $database->connect();

            if ($pdo) {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :input OR username = :input LIMIT 1");
                $stmt->execute([':input' => $this->usernameOrEmail]);
                $user = $stmt->fetch();

                if ($user && password_verify($this->password, $user['password_hash'])) {
                    $this->userData = $user;
                    return true;
                } else {
                    $this->errors['login'] = "Invalid credentials.";
                }
            }
        } catch (PDOException $e) {
            error_log("DB error in authenticateUser: " . $e->getMessage());
            $this->errors['database'] = "System error. Please try again later.";
        }
        return false;
    }

    public function getUserData(): ?array {
        return $this->userData;
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
