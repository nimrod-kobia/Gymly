<?php
require_once "../autoload.php";
SessionManager::requireAuth();

$pdo = (new Database())->connect();
$userId = SessionManager::getUserId();
$errors = [];
$success = '';

// simple CSRF
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));

// handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request.';
    } else {
        $full_name = trim($_POST['full_name'] ?? '');
        $username  = trim($_POST['username'] ?? '');
        $email     = trim($_POST['email'] ?? '');

        if ($full_name === '' || $username === '' || $email === '') {
            $errors[] = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address.';
        } else {
            // check uniqueness for username/email
            $check = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id <> ? LIMIT 1");
            $check->execute([$username, $email, $userId]);
            if ($check->fetch()) {
                $errors[] = 'Username or email already in use.';
            } else {
                $upd = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, updated_at = NOW() WHERE id = ?");
                $ok = $upd->execute([$full_name, $username, $email, $userId]);
                if ($ok) {
                    $success = 'Profile updated successfully.';
                } else {
                    $errors[] = 'Could not update profile, try again later.';
                }
            }
        }
    }
}

// fetch latest user row (matching datatables columns)
$stmt = $pdo->prepare("SELECT id, full_name, username, email, role, is_verified, created_at, updated_at FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo "<p class='text-danger text-center'>User not found.</p>";
    exit;
}

$pageTitle = "Profile | Gymly";
include '../template/layout.php';
?>

<main class="container py-5 text-white">
  <div class="row justify-content-center">
    <div class="col-lg-9">
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

      <div class="card bg-dark border-secondary mb-4">
        <div class="card-body">
          <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
            <div class="flex-grow-1">
              <h2 class="mb-1 text-white"><?php echo htmlspecialchars($user['full_name']); ?></h2>
              <p class="mb-1 text-white">@<?php echo htmlspecialchars($user['username']); ?></p>
              <p class="mb-0 text-white"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($user['email']); ?></p>
            </div>

            <div class="text-md-end">
              <?php if (!empty($user['is_verified'])): ?>
                <span class="badge bg-success">Verified</span>
              <?php else: ?>
                <span class="badge bg-warning text-dark">Unverified</span>
              <?php endif; ?>
              <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars(ucfirst($user['role'] ?? 'member')); ?></span>
            </div>
          </div>
        </div>

        <div class="card-footer bg-transparent border-top border-secondary text-white small">
          <div class="d-flex justify-content-between flex-wrap">
            <div>Member ID: <strong><?php echo htmlspecialchars($user['id']); ?></strong></div>
            <div>Joined: <strong><?php echo date('d-m-Y H:i', strtotime($user['created_at'])); ?></strong></div>
            <div>Last update: <strong><?php echo $user['updated_at'] ? date('d-m-Y H:i', strtotime($user['updated_at'])) : '-'; ?></strong></div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-5">
          <div class="card bg-dark border-secondary">
            <div class="card-header bg-transparent border-secondary">
              <h5 class="mb-0 text-white">Edit Profile</h5>
            </div>
            <div class="card-body">
              <form method="post" novalidate>
                <input type="hidden" name="action" value="update_profile">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="mb-3">
                  <label class="form-label text-white">Full name</label>
                  <input name="full_name" class="form-control bg-black text-white border-dark" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>

                <div class="mb-3">
                  <label class="form-label text-white">Username</label>
                  <input name="username" class="form-control bg-black text-white border-dark" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>

                <div class="mb-3">
                  <label class="form-label text-white">Email</label>
                  <input type="email" name="email" class="form-control bg-black text-white border-dark" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary">Save changes</button>
                  <a href="changePassword.php" class="btn btn-outline-light">Change password</a>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card bg-dark border-secondary h-100">
            <div class="card-header bg-transparent border-secondary">
              <h5 class="mb-0 text-white">Account Details (from users table)</h5>
            </div>
            <div class="card-body">
              <div class="row gy-3">
                <div class="col-md-6">
                  <label class="text-muted d-block">Full name</label>
                  <div class="text-white"><?php echo htmlspecialchars($user['full_name']); ?></div>
                </div>
                <div class="col-md-6">
                  <label class="text-muted d-block">Username</label>
                  <div class="text-white">@<?php echo htmlspecialchars($user['username']); ?></div>
                </div>
                <div class="col-md-6">
                  <label class="text-muted d-block">Email</label>
                  <div class="text-white"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>
                <div class="col-md-6">
                  <label class="text-muted d-block">Validation status</label>
                  <div class="text-white"><?php echo !empty($user['is_verified']) ? 'Verified' : 'Unverified'; ?></div>
                </div>
                <div class="col-md-6">
                  <label class="text-muted d-block">Role</label>
                  <div class="text-white"><?php echo htmlspecialchars(ucfirst($user['role'] ?? 'member')); ?></div>
                </div>
                <div class="col-md-6">
                  <label class="text-muted d-block">Member since</label>
                  <div class="text-white"><?php echo date('d-m-Y H:i', strtotime($user['created_at'])); ?></div>
                </div>
              </div>
            </div>
            <div class="card-footer bg-transparent border-top border-secondary text-end">
  
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

<?php include '../template/footer.php'; ?>
