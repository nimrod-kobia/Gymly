<?php
class Forms {
    private function alertMessages() {
        $html = '';
        if (isset($_GET['error']) && !empty($_GET['error'])) {
            $html .= '<div class="alert alert-danger">';
            $errors = explode("|", $_GET['error']);
            foreach ($errors as $error) {
                if (!empty($error)) {
                    $html .= htmlspecialchars($error) . "<br>";
                }
            }
            $html .= '</div>';
        }

        if (isset($_GET['success'])) {
            $html .= '<div class="alert alert-success">
                        <strong>Success!</strong> ' . htmlspecialchars($_GET['success']) . '
                      </div>';
        }
        return $html;
    }

    private function submitButton($value, $name, $class = 'btn-primary') {
        return '<button type="submit" name="' . $name . '" class="btn ' . $class . '">' . $value . '</button>';
    }

    public function signUp() {
        ob_start();
        // <existing signUp() code here — unchanged>
        ?>
        <!-- your current signUp() HTML -->
        <?php
        return ob_get_clean();
    }

    public function signIn(): string {
        ob_start();
        // <existing signIn() code here — unchanged>
        ?>
        <!-- your current signIn() HTML -->
        <?php
        return ob_get_clean();
    }

    // NEW METHOD: Verify Form
    public function verifyForm(): string {
        ob_start();
        ?>
        <div class="auth-container">
            <div class="auth-card">
                <div class="logo-container">
                    <img src="../assets/images/logo.png" alt="Gymly" class="logo">
                    <h1 class="auth-title">Verify Account</h1>
                    <p class="auth-subtitle">Enter the verification code sent to your email</p>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="../handlers/verifyHandler.php">
                        <div class="form-group mb-3">
                            <label for="code">Verification Code</label>
                            <input type="text" name="code" id="code" class="form-control" required>
                        </div>

                        <button type="submit" name="verify" class="btn btn-primary w-100">Verify</button>
                    </form>

                    <p class="auth-footer">
                        Didn't receive a code? <a href="signIn.php">Sign In</a>
                    </p>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>
