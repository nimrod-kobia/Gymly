<?php
require_once "../autoload.php";
SessionManager::requireAuth();

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));

$pdo = (new Database())->connect();
$userId = SessionManager::getUserId();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request.';
    } else {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($current === '' || $new === '' || $confirm === '') {
            $errors[] = 'All fields are required.';
        } elseif ($new !== $confirm) {
            $errors[] = 'New passwords do not match.';
        } elseif (strlen($new) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        } else {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row || !password_verify($current, $row['password'])) {
                $errors[] = 'Current password is incorrect.';
            } else {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                if ($upd->execute([$hash, $userId])) {
                    $success = 'Password changed successfully.';
                } else {
                    $errors[] = 'Could not update password, try again later.';
                }
            }
        }
    }
}

$pageTitle = "Change Password | Gymly";
include '../template/layout.php';
?>

<main class="container py-5 text-white">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="card bg-dark border-secondary">
        <div class="card-header bg-transparent border-secondary">
          <h5 class="mb-0 text-white">Change Password</h5>
        </div>
        <div class="card-body">
          <form method="post" novalidate>
            <input type="hidden" name="action" value="change_password">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="mb-3">
              <label class="form-label text-white">Current password</label>
              <input type="password" name="current_password" class="form-control bg-black text-white border-dark" required>
            </div>

            <div class="mb-3">
              <label class="form-label text-white">New password</label>
              <input type="password" name="new_password" class="form-control bg-black text-white border-dark" required>
              <div class="form-text text-muted">Minimum 8 characters.</div>
            </div>

            <div class="mb-3">
              <label class="form-label text-white">Confirm new password</label>
              <input type="password" name="confirm_password" class="form-control bg-black text-white border-dark" required>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">Update password</button>
              <a href="profile.php" class="btn btn-outline-light">Back to profile</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include '../template/footer.php'; ?>
