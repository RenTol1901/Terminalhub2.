<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['full_name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $error = 'Enter your name, a valid email, and a password of at least 8 characters.';
    } else {
        try {
            $stmt = db()->prepare("INSERT INTO users (full_name,email,password_hash,role) VALUES (?,?,?,'passenger')");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $_SESSION['flash'] = 'Your passenger account has been created. Please sign in.';
            redirect('login.php');
        } catch (PDOException $ex) {
            $error = $ex->getCode() === '23000' ? 'That email is already registered.' : 'Could not create account. Check the database configuration.';
        }
    }
}
$title = 'Passenger sign up';
require __DIR__ . '/includes/header.php';
?>
<section class="auth-wrap"><div class="auth-card">
  <div class="eyebrow">PASSENGER ACCOUNT</div><h1>Create account</h1><p class="muted">Sign up to browse trips and request bookings.</p>
  <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post" class="stack">
    <label>Full name<input name="full_name" required minlength="2" autocomplete="name"></label>
    <label>Email address<input type="email" name="email" required autocomplete="email"></label>
    <label>Password<input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
    <button class="button full" type="submit">Create passenger account</button>
  </form>
  <p class="muted small">Already registered? <a href="<?= e(APP_BASE) ?>/login.php">Sign in</a>.</p>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
