<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
if (current_user()) redirect(dashboard_for_role(current_user()['role']));
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && $password !== '') {
        try {
            $stmt = db()->prepare('SELECT id, full_name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                unset($user['password_hash']);
                $_SESSION['user'] = $user;
                redirect(dashboard_for_role($user['role']));
            }
        } catch (PDOException $ex) {
            $error = 'Could not connect to the database. Import database/terminalhub.sql and check config.php.';
        }
    }
    if ($error === '') $error = 'Email or password was not recognized.';
}
$title = 'Log in';
require __DIR__ . '/includes/header.php';
?>
<section class="auth-wrap">
  <div class="auth-card">
    <div class="eyebrow">WELCOME BACK</div><h1>Sign in</h1><p class="muted">Access your TerminalHub workspace.</p>
    <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="stack">
      <label>Email address<input type="email" name="email" required autocomplete="username" placeholder="you@example.com"></label>
      <label>Password<input type="password" name="password" required autocomplete="current-password" placeholder="Your password"></label>
      <button class="button full" type="submit">Sign in securely</button>
    </form>
    <p class="muted small">Passenger account needed? <a href="<?= e(APP_BASE) ?>/register.php">Create one</a>.</p>
    <details class="demo"><summary>Local demo accounts</summary>
      <p>Admin: <code>admin@terminalhub.local</code><br>Staff: <code>staff@terminalhub.local</code><br>Passenger: <code>passenger@terminalhub.local</code><br>Password for all: <code>password</code></p>
    </details>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
