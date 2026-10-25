<?php
require_once __DIR__ . '/../includes/auth.php';
$user = require_role('admin');
$counts = ['users'=>0,'routes'=>0,'trips'=>0,'bookings'=>0];
try {
  foreach (['users','routes','trips','bookings'] as $table) $counts[$table] = (int)db()->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
  $users = db()->query('SELECT full_name,email,role,created_at FROM users ORDER BY created_at DESC LIMIT 6')->fetchAll();
  $trips = db()->query('SELECT t.id,t.departure_time,t.fare,t.seats_available,r.origin,r.destination,v.operator_name,v.vehicle_type FROM trips t JOIN routes r ON r.id=t.route_id JOIN vehicles v ON v.id=t.vehicle_id ORDER BY t.departure_time LIMIT 6')->fetchAll();
} catch (PDOException $ex) { $error = 'Database tables are not ready. Import database/terminalhub.sql.'; }
$title = 'Admin dashboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-heading"><div><div class="eyebrow">CONTROL CENTER</div><h1>Admin dashboard</h1><p class="muted">System overview and recent activity.</p></div><span class="role-pill">ADMIN</span></div>
<?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<div class="stats">
<?php foreach (['users'=>'Registered users','routes'=>'Routes','trips'=>'Scheduled trips','bookings'=>'Booking requests'] as $key=>$label): ?>
<div class="stat-card"><span><?= e($label) ?></span><strong><?= (int)$counts[$key] ?></strong><small>Current records</small></div>
<?php endforeach; ?>
</div>
<section class="panel"><div class="panel-head"><h2>Scheduled trips</h2><span class="muted small">Next departures</span></div>
<?php if (!empty($trips)): ?><div class="table-wrap"><table><thead><tr><th>Route</th><th>Operator / vehicle</th><th>Departure</th><th>Fare</th><th>Seats</th></tr></thead><tbody>
<?php foreach ($trips as $t): ?><tr><td><strong><?= e($t['origin']) ?></strong><span class="route-arrow"> → </span><?= e($t['destination']) ?></td><td><?= e($t['operator_name']) ?><div class="muted small"><?= e($t['vehicle_type']) ?></div></td><td><?= e(date('M j, Y g:i A', strtotime($t['departure_time']))) ?></td><td>₱<?= number_format((float)$t['fare'],2) ?></td><td><?= (int)$t['seats_available'] ?></td></tr><?php endforeach; ?>
</tbody></table></div><?php else: ?><p class="empty">No trips available yet.</p><?php endif; ?></section>
<section class="panel"><div class="panel-head"><h2>Recently registered users</h2></div>
<?php if (!empty($users)): ?><div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr></thead><tbody><?php foreach ($users as $u): ?><tr><td><?= e($u['full_name']) ?></td><td><?= e($u['email']) ?></td><td><span class="status"><?= e(ucfirst($u['role'])) ?></span></td><td><?= e(date('M j, Y', strtotime($u['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><p class="empty">No users found.</p><?php endif; ?></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
