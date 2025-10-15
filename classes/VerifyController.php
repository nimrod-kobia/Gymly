<?php
class VerifyController {
    private string $username;
    private string $code;
    private array $errors = [];
    private ?PDO $pdo = null;

    public function __construct(string $username, string $code) {
        $this->username = trim($username);
        $this->code = trim($code);
        $this->pdo = (new Database())->connect();
    }

    public function validateInputs(): bool {
        if (empty($this->username)) $this->errors['username'] = "Username is required.";
        if (empty($this->code)) $this->errors['code'] = "Verification code is required.";
        return empty($this->errors);
    }

    public function checkCode(): bool {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM users
                WHERE username = :username
                  AND verification_code = :code
                  AND code_expiry > NOW()
                  AND is_verified = FALSE
                LIMIT 1
            ");
            $stmt->execute([
                ':username' => $this->username,
                ':code' => $this->code
            ]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) return true;

            $this->errors['verification'] = "Invalid or expired verification code.";
            return false;
        } catch (PDOException $e) {
            $this->errors['database'] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    public function markVerified(): bool {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users
                SET is_verified = TRUE,
                    verification_code = NULL,
                    code_expiry = NULL
                WHERE username = :username
            ");
            return $stmt->execute([':username' => $this->username]);
        } catch (PDOException $e) {
            $this->errors['database'] = "Failed to verify user: " . $e->getMessage();
            return false;
        }
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
?>

