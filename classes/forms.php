<?php
class Forms {
    // Shared header section (logo + title + subtitle)
    private function headerSection($title, $subtitle) {
        return '
        <div class="logo-container">
            <img src="assets/images/logo.png" alt="Logo" class="logo">
            <h1 class="auth-title">' . htmlspecialchars($title) . '</h1>
            <p class="auth-subtitle">' . htmlspecialchars($subtitle) . '</p>
        </div>';
    }

    // Shared error/success message handler
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
                        <strong>Success</strong> ' . htmlspecialchars($_GET['success']) . '
                      </div>';
        }
        return $html;
    }

    // Shared button generator
    private function submitButton($value, $name, $class = 'btn-primary') {
        return '<button type="submit" name="' . $name . '" class="btn ' . $class . '">' . $value . '</button>';
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
    public function signIn() {
        ob_start();
?>
<div class="auth-container">
    <div class="auth-card">
        <?= $this->headerSection("Welcome Back", "Please enter your details"); ?>
        <?= $this->alertMessages(); ?>

        <form action="../handlers/signInHandler.php" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember for 30 days</label>
                </div>
                <a href="#" class="forgot-link">Forgot password?</a>
            </div>

            <div class="d-grid gap-2">
                <?= $this->submitButton("Sign In", "signIn"); ?>
            </div>
        </form>
    </div>
</div>
<?php
        return ob_get_clean();
    }
}
?>
