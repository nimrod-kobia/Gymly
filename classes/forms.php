<?php
class Forms {

    // Shared header section (logo + title + subtitle)
    private function headerSection(string $title, string $subtitle): string {
        $logoPath = "../assets/images/logo.png"; // fixed relative path
        return '
        <div class="logo-container text-center mb-4">
            <img src="' . htmlspecialchars($logoPath) . '" alt="Gymly" class="logo mb-2">
            <h1 class="auth-title">' . htmlspecialchars($title) . '</h1>
            <p class="auth-subtitle">' . htmlspecialchars($subtitle) . '</p>
        </div>';
    }

    // Shared error/success messages
    private function alertMessages(): string {
        $html = '';
        if (!empty($_GET['error'])) {
            $html .= '<div class="alert alert-danger">';
            $errors = explode("|", $_GET['error']);
            foreach ($errors as $error) {
                if (!empty($error)) $html .= htmlspecialchars($error) . "<br>";
            }
            $html .= '</div>';
        }
        if (!empty($_GET['success'])) {
            $html .= '<div class="alert alert-success">'
                  . '<strong>Success:</strong> ' . htmlspecialchars($_GET['success']) .
                  '</div>';
        }
        return $html;
    }

    // Shared submit button
    private function submitButton(string $value, string $name, string $class = 'btn-primary'): string {
        return '<button type="submit" name="' . htmlspecialchars($name) . '" class="btn ' . htmlspecialchars($class) . '">' . htmlspecialchars($value) . '</button>';
    }

    // Signup form
    public function signUp(): string {
        ob_start();
        ?>
        <div class="auth-container">
            <div class="auth-card">
                <?= $this->headerSection("Gymly", "Start your fitness journey today"); ?>
                <?= $this->alertMessages(); ?>
                <form action="../handlers/signUpHandler.php" method="post">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" name="fullname" id="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="cpassword" class="form-label">Confirm Password</label>
                        <input type="password" name="cpassword" id="cpassword" class="form-control" required>
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
                <form method="post" action="../handlers/signInHandler.php">
                    <div class="mb-3">
                        <label for="usernameOrEmail" class="form-label">Username or Email</label>
                        <input type="text" name="usernameOrEmail" id="usernameOrEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="d-grid gap-2">
                        <?= $this->submitButton("Sign In", "signIn", "btn-primary w-100"); ?>
                    </div>
                </form>
                <p class="auth-footer mt-3">
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
                <?= $this->headerSection("Verify Account", "Enter the code sent to your email"); ?>
                <?= $this->alertMessages(); ?>
                <form method="post" action="../handlers/verifyHandler.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label">Verification Code</label>
                        <input type="text" name="code" id="code" class="form-control" required>
                    </div>
                    <div class="d-grid gap-2">
                        <?= $this->submitButton("Verify", "verify"); ?>
                    </div>
                </form>
                <p class="auth-footer mt-3">
                    Didn't receive a code? <a href="signInPage.php">Sign In</a>
                </p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>
