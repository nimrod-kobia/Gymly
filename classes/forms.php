<?php
class Forms {
    public function signUp() {
        ob_start();
?>
<div class="auth-container">
    <div class="auth-card">
        <div class="logo-container">
            <img src="assets\images\logo.png" alt="Logo" class="logo">
            <h1 class="auth-title">Gymly</h1>
            <p class="auth-subtitle">Start your fitness journey today</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <strong>Success</strong> Your account has been created. Please check your email.
            </div>
        <?php endif; ?>

        <form action="classes/SignupController.php" method="post">
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
                <?php echo $this->submitButton("Create Account", "signUp"); ?>
            </div>
        </form>
    </div>
</div>
<?php
        return ob_get_clean();
    }

    public function signIn() {
        ob_start();
?>
<div class="auth-container">
    <div class="auth-card">
        <div class="logo-container">
            <img src="assets\images\logo.png" alt="Logo" class="logo">
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Please enter your details</p>
        </div>

        <button class="btn btn-google w-100 mb-3">
            <i class="bi bi-google me-2"></i>Sign in with Google 
        </button>

        <div class="divider"><span class="divider-text">or</span></div>

        <form action="classes/SigninController.php" method="post">
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
                <?php echo $this->submitButton("Sign In", "signIn"); ?>
            </div>
        </form>
    </div>
</div>
<?php
        return ob_get_clean();
    }

    private function submitButton($value, $name, $class = 'btn-primary') {
        return '<button type="submit" name="' . $name . '" class="btn ' . $class . '">' . $value . '</button>';
    }
}
?>
