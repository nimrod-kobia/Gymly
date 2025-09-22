<?php
class VerifyController {
    private string $inputCode;
    private array $errors = [];

    public function __construct(string $inputCode) {
        $this->inputCode = trim($inputCode);
    }

    public function validateCode(): bool {
        if (empty($this->inputCode)) {
            $this->errors['code'] = "Verification code is required.";
        } elseif (!preg_match('/^\d{6}$/', $this->inputCode)) {
            $this->errors['code'] = "Invalid code format.";
        }
        return empty($this->errors);
    }

    public function checkCode(): bool {
        if (empty($_SESSION['verification_code'])) {
            $this->errors['code'] = "No verification code found. Please try signing up again.";
            return false;
        }

        if ($this->inputCode === $_SESSION['verification_code']) {
            return true;
        } else {
            $this->errors['code'] = "Invalid verification code.";
            return false;
        }
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
