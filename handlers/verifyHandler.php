<?php
require_once "../autoload.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['verify'])) {
    $username = $_POST['username'] ?? '';
    $code = $_POST['code'] ?? '';

    // Check if user is allowed to verify (in correct session state)
    if (!SessionManager::isInVerificationProcess() && !SessionManager::isInSignupProcess()) {
        header("Location: ../pages/signUpPage.php?error=Invalid+verification+request");
        exit();
    }

    $verify = new VerifyController($username, $code);

    if ($verify->validateInputs() && $verify->checkCode()) {
        if ($verify->markVerified()) {
            // Convert verification session to logged-in session
            if (SessionManager::isInVerificationProcess() || SessionManager::isInSignupProcess()) {
                $userId = SessionManager::getUserId();
                $username = SessionManager::getUsername();
                
                // Get updated user data from database
                $database = new Database();
                $pdo = $database->connect();
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
                $stmt->execute([':id' => $userId]);
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($userData) {
                    // Upgrade session to logged-in state
                    SessionManager::startLoginSession($userData);
                    //  Redirect to home.php instead of dashboard.php
                    header("Location: ../pages/home.php?success=verified");
                    exit();
                }
            }
            
            header("Location: ../pages/signInPage.php?success=Account+verified.+Please+sign+in.");
            exit();
        } else {
            $errors = $verify->getErrors();
            header("Location: ../pages/verify.php?error=" . urlencode(implode("|", $errors)));
            exit();
        }
    } else {
        $errors = $verify->getErrors();
        header("Location: ../pages/verify.php?error=" . urlencode(implode("|", $errors)));
        exit();
    }
} else {
    header("Location: ../pages/verify.php?error=Invalid+request");
    exit();
}
?>