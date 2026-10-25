<?php
require_once __DIR__ . '/config.php';
if (!empty($_SESSION['user'])) {
    require_once __DIR__ . '/includes/auth.php';
    redirect(dashboard_for_role($_SESSION['user']['role']));
}
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="eyebrow">VAN & BUS TERMINAL MANAGEMENT</div>
  <h1>Your journey starts<br><span>at TerminalHub.</span></h1>
  <p>Manage terminal operations, coordinate trips, and browse available departures in one simple workspace.</p>
  <div class="actions"><a class="button" href="<?= e(APP_BASE) ?>/login.php">Sign in</a><a class="button button-ghost" href="<?= e(APP_BASE) ?>/register.php">Create passenger account</a></div>
</section>
<section class="feature-grid">
  <article class="feature"><div class="feature-icon">▦</div><h3>Admin control</h3><p>View system activity, users, routes, and scheduled trips.</p></article>
  <article class="feature"><div class="feature-icon">◷</div><h3>Staff operations</h3><p>Review scheduled departures and keep terminal operations organized.</p></article>
  <article class="feature"><div class="feature-icon">➜</div><h3>Passenger access</h3><p>Browse trips and submit booking requests from your dashboard.</p></article>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
