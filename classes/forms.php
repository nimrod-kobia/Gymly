<?php
class Forms{
    public function signUp(){
        ob_start();
?>
<div class="auth-container">
    <div class="auth-card">
        <div class="logo-container">
            <img src="assets/images/logo.png" alt="Logo" class="logo">
            <h1 class="auth-title">Gymly</h1>
            <p class="auth-subtitle">Start your fitness journey today</p>
        </div>
        <form action="classes\SignupController.php" method="post">
            <div class="mb-3">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname"required>
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
        </form>
    </div>
</div>
<?php
        return ob_get_clean();
    }
    public function signIn(){
        ob_start();
        return ob_get_clean();
    }
    private function submitButton($value, $name,$class = 'btn-primary'){
        return '<button type="submit" name="'.$name.'" class="btn '.$class.'">'.$value.'</button>';
    }
}
?>