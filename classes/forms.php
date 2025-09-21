<?php
class Forms {
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
?>
<div class="auth-container">
    <div class="auth-card">
        <div class="logo-container">
            <img src="../assets/images/logo.png" alt="Gymly" class="logo">
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Start your fitness journey today</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <strong>Success!</strong> Your account has been created. Please check your email.
        </div>
        <?php endif; ?>

        <form action="../handlers/signupHandler.php" method="post">
            <div class="mb-3">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter your full name" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
            </div>          
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>        
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
            </div>   
            <div class="mb-3">
                <label for="cpassword" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Confirm your password" required>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" name="signUp" class="btn btn-primary">Create Account</button>
            </div>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="signIn.php" class="auth-link">Sign In</a></p>
            </div>
        </form>
    </div>
</div>
<?php
        return ob_get_clean();
    }

    public function signIn(): string {
        ob_start();
        ?>
        <div class="auth-container">
            <div class="auth-card">
                <div class="logo-container">
                    <img src="../assets/images/logo.png" alt="Gymly" class="logo">
                <h1 class="auth-title">Sign In</h1>
                <p class="auth-subtitle">Welcome back! Please enter your credentials.</p>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="../handlers/signInHandler.php">
                    <div class="form-group mb-3">
                        <label for="usernameOrEmail">Username or Email</label>
                        <input type="text" name="usernameOrEmail" id="usernameOrEmail" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <button type="submit" name="signIn" class="btn btn-primary w-100">Sign In</button>
                </form>

                <p class="auth-footer">
                    Don't have an account? <a href="signUpPage.php">Sign up</a>
                </p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>