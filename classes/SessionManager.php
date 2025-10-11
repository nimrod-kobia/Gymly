<?php
class SessionManager {
    
    // Start session when user begins signup
    public static function startSignupSession($userId, $username, $email) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['auth_stage'] = 'signup'; // Track authentication stage
        $_SESSION['session_started'] = time();
    }
    
    // Start session when user successfully logs in
    public static function startLoginSession($userData) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['username'] = $userData['username'];
        $_SESSION['email'] = $userData['email'];
        $_SESSION['full_name'] = $userData['full_name'];
        $_SESSION['is_verified'] = $userData['is_verified'];
        $_SESSION['auth_stage'] = 'logged_in';
        $_SESSION['session_started'] = time();
        $_SESSION['last_activity'] = time(); // For timeout tracking
    }
    
    // Start session for verification process
    public static function startVerificationSession($userId, $username) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['auth_stage'] = 'verification';
        $_SESSION['session_started'] = time();
    }
    
    // Check if user is fully logged in
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && 
               isset($_SESSION['auth_stage']) && 
               $_SESSION['auth_stage'] === 'logged_in' &&
               self::isSessionValid();
    }
    
    // Check if user is in signup process
    public static function isInSignupProcess() {
        return isset($_SESSION['auth_stage']) && $_SESSION['auth_stage'] === 'signup';
    }
    
    // Check if user is in verification process
    public static function isInVerificationProcess() {
        return isset($_SESSION['auth_stage']) && $_SESSION['auth_stage'] === 'verification';
    }
    
    // Validate session timeout (24 hours of inactivity)
    public static function isSessionValid() {
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return true;
        }
        
        $timeout = 86400; // 24 hours in seconds
        if (time() - $_SESSION['last_activity'] > $timeout) {
            self::destroySession();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    // Update last activity time
    public static function updateActivity() {
        $_SESSION['last_activity'] = time();
    }
    
    // Completely destroy session (logout)
    public static function destroySession() {
        // Clear session data
        $_SESSION = array();
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    // Helper methods to get user data
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function getUsername() {
        return $_SESSION['username'] ?? null;
    }
    
    // Redirect to login if not authenticated
    public static function requireAuth() {
        if (!self::isLoggedIn()) {
            header("Location: ../pages/signInPage.php");
            exit();
        }
    }
    
    // Redirect to signup if not in verification process
    public static function requireVerification() {
        if (!self::isInVerificationProcess()) {
            header("Location: ../pages/signUpPage.php");
            exit();
        }
    }
}
?>