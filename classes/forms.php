<?php
class Forms {
    // Shared header section (logo + title + subtitle)
    private function headerSection($title, $subtitle) {
        // Fixed path: go one level up from pages/
        $logoPath = "../assets/images/logo.png";

        return '
        <div class="logo-container">
            <img src="' . htmlspecialchars($logoPath) . '" alt="Gymly" class="logo">
            <h1 class="auth-title">' . htmlspecialchars($title) . '</h1>
            <p class="auth-subtitle">' . htmlspecialchars($subtitle) . '</p>
        </div>';
    }

    // Shared error/success messages
    private function alertMessages() {
        $html = '';
        if (isset($_GET['error']) && !empty($_GET['error'])) {
            $html .= '<div class="alert alert-danger">';
            $errors = explode("|", $_GET['error']);
            foreach ($errors as $error) {
                if (!empty($error)) $html .= htmlspecialchars($error) . "<br>";
            }
            $html .= '</div>';
        }

        if (isset($_GET['success'])) {
            $html .= '<div class="alert alert-success">
                        <strong>Success</strong> ' . htmlspecialchars($_GET['success']) . '
                      </div>';
        }
        return $html;
    }

    // Shared button
    private function submitButton($value, $name, $class = 'btn-primary') {
        return '<button type="submit" name="' . htmlspecialchars($name) . '" class="btn ' . htmlspecialchars($class) . '">' . htmlspecialchars($value) . '</button>';
    }

    // Signup form
    public function signUp() {
        ob_start();
        ?>
        <div class="auth-container">
            <div class="auth-card">
                <?= $this->headerSection("Gymly", "Start your fitness journey today"); ?>
                <?= $this->alertMessages(); ?>
                <form action="../handlers/signUpHandler.php" method="post">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="cpassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="cpassword" name="cpassword" required>
                    </div>
                    <div class="d-grid gap-2">
                        <?= $this->submitButton("Create Account", "signUp"); ?>
                    </div>
                </form>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    // Signin form
    public function signIn(): string {
        ob_start();
        ?>
        <div class="auth-container">
            <div class="auth-card">
                <?= $this->headerSection("Sign In", "Welcome back! Please enter your credentials"); ?>
                <?= $this->alertMessages(); ?>
                <form method="POST" action="../handlers/signInHandler.php">
                    <div class="mb-3">
                        <label for="usernameOrEmail">Username or Email</label>
                        <input type="text" name="usernameOrEmail" id="usernameOrEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="d-grid gap-2">
                        <?= $this->submitButton("Sign In", "signIn", "btn-primary w-100"); ?>
                    </div>
                </form>
                <p class="auth-footer">
                    Don’t have an account? <a href="signUpPage.php">Sign up</a>
                </p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    // Verify form
    public function verifyForm(): string {
        ob_start();
        ?>
        <div class="auth-container">
            <div class="auth-card">
                <?= $this->headerSection("Verify Account", "Enter the verification code sent to your email"); ?>
                <?= $this->alertMessages(); ?>
                <form method="POST" action="../handlers/verifyHandler.php">
                    <div class="mb-3">
                        <label for="code">Verification Code</label>
                        <input type="text" name="code" id="code" class="form-control" required>
                    </div>
                    <div class="d-grid gap-2">
                        <?= $this->submitButton("Verify", "verify"); ?>
                    </div>
                </form>
                <p class="auth-footer">
                    Didn't receive a code? <a href="signInPage.php">Sign In</a>
                </p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>
