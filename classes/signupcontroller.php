<?php
class SignupController {
    private string $fullname;
    private string $username;
    private string $email;
    private string $password;
    private string $cpassword;
    private array $errors = [];
    private ?PDO $pdo = null;

    public function __construct(string $fullname, string $username, string $email, string $password, string $cpassword) {
        $this->fullname = trim($fullname);
        $this->username = trim($username);
        $this->email = trim($email);
        $this->password = $password;
        $this->cpassword = $cpassword;
        $this->pdo = (new Database())->connect();
    }

    // Validate user inputs
    public function validateInputs(): bool {
        if (empty($this->fullname)) $this->errors['fullname'] = "Full name is required.";
        if (empty($this->username)) $this->errors['username'] = "Username is required.";
        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) $this->errors['email'] = "Valid email is required.";
        if (empty($this->password)) $this->errors['password'] = "Password is required.";
        if ($this->password !== $this->cpassword) $this->errors['cpassword'] = "Passwords do not match.";
        return empty($this->errors);
    }

    // Check if username or email already exists
    public function checkUserExists(): void {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1");
            $stmt->execute([
                ':username' => $this->username,
                ':email' => $this->email
            ]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                if ($user['username'] === $this->username) $this->errors['username'] = "Username already taken.";
                if ($user['email'] === $this->email) $this->errors['email'] = "Email already registered.";
            }
        } catch (PDOException $e) {
            $this->errors['database'] = "Database error: " . $e->getMessage();
        }
    }

    // Create new user and return user ID
    public function createUser(): ?int {
        try {
            $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

            $stmt = $this->pdo->prepare("
                INSERT INTO users (full_name, username, email, password_hash, role, created_at, updated_at)
                VALUES (:fullname, :username, :email, :password_hash, 'user', NOW(), NOW())
                RETURNING id
            ");
            $stmt->execute([
                ':fullname' => $this->fullname,
                ':username' => $this->username,
                ':email' => $this->email,
                ':password_hash' => $passwordHash
            ]);

            // Return newly created user ID
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['id'] : null;
        } catch (PDOException $e) {
            $this->errors['database'] = "Failed to create user: " . $e->getMessage();
            return null;
        }
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
?>
