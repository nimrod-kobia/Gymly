<?php
// changePassword.php — Change user password with validation + safe DB handling

// start session early
if (session_status() === PHP_SESSION_NONE) session_start();

require_once "../autoload.php";

// require authenticated user
SessionManager::requireAuth();

$pageTitle = "Change Password | Gymly";

$pdo = null;
try {
    $pdo = (new Database())->connect();
    // ensure PDO throws exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    error_log("changePassword.php DB connect error: " . $e->getMessage());
    http_response_code(500);
    echo "Server error. Check logs.";
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$userId = SessionManager::getUserId();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    try {
        // basic CSRF check
        $postedToken = $_POST['csrf_token'] ?? '';
        if (!hash_equals((string)$_SESSION['csrf_token'], (string)$postedToken)) {
            throw new RuntimeException('Invalid request (CSRF).');
        }

        $current = (string)($_POST['current_password'] ?? '');
        $new = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');

        // validations
        if ($current === '' || $new === '' || $confirm === '') {
            throw new RuntimeException('All fields are required.');
        }
        if ($new !== $confirm) {
            throw new RuntimeException('New passwords do not match.');
        }
        if (strlen($new) < 8) {
            throw new RuntimeException('New password must be at least 8 characters long.');
        }

        // fetch current row (attempt to adapt to whatever column name your users table uses)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new RuntimeException('Account not found.');
        }

        // detect password column name (common variants)
        $possible = [
            'password','pwd','passwd','password_hash','hashed_password','pass','user_password','secret','pw'
        ];
        $pwField = null;
        foreach ($possible as $p) {
            if (array_key_exists($p, $row)) {
                $pwField = $p;
                break;
            }
            // also check uppercase variant (some PG returns lowercase, but just in case)
            if (array_key_exists(strtoupper($p), $row)) {
                $pwField = strtoupper($p);
                break;
            }
        }

        if ($pwField === null) {
            // helpful log for debugging schema mismatch
            error_log("changePassword.php: could not find password column for user id {$userId}. Columns: " . implode(',', array_keys($row)));
            throw new RuntimeException('Account password field not found. Contact support.');
        }

        $storedHash = $row[$pwField] ?? '';
        if (empty($storedHash)) {
            throw new RuntimeException('Account password not set. Contact support.');
        }

        if (!password_verify($current, $storedHash)) {
            throw new RuntimeException('Current password is incorrect.');
        }

        // hash and update — update the same column we detected
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $updSql = "UPDATE users SET {$pwField} = ?, updated_at = NOW() WHERE id = ?";
        $upd = $pdo->prepare($updSql);
        $ok = $upd->execute([$hash, $userId]);
        if (!$ok) {
            throw new RuntimeException('Unable to update password. Try again later.');
        }

        $success = 'Password changed successfully.';
        // rotate CSRF token after successful action
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    } catch (Exception $ex) {
        // log unexpected exceptions and show generic message
        error_log("changePassword.php error (user {$userId}): " . $ex->getMessage());
        $errors[] = 'Server error. Please try again later.';
    }
}

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
