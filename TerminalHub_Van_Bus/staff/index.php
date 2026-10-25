<?php
require_once __DIR__ . '/../includes/auth.php';
$user = require_role('staff','admin');
try {
  $stmt = db()->query('SELECT t.id,t.departure_time,t.arrival_time,t.fare,t.seats_available,t.status,r.origin,r.destination,v.operator_name,v.vehicle_type,v.plate_number FROM trips t JOIN routes r ON r.id=t.route_id JOIN vehicles v ON v.id=t.vehicle_id ORDER BY t.departure_time LIMIT 50');
  $trips = $stmt->fetchAll();
} catch (PDOException $ex) { $trips = []; $error = 'Database tables are not ready. Import database/terminalhub.sql.'; }
$title = 'Staff dashboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-heading"><div><div class="eyebrow">TERMINAL OPERATIONS</div><h1>Staff dashboard</h1><p class="muted">Review departures, arrival times, and vehicle details.</p></div><span class="role-pill">STAFF</span></div>
<?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<section class="panel"><div class="panel-head"><h2>Trip schedule</h2><input id="tableSearch" class="search-input" placeholder="Filter trips…" aria-label="Filter trips"></div>
<?php if ($trips): ?><div class="table-wrap"><table id="filterTable"><thead><tr><th>Route</th><th>Vehicle</th><th>Departure</th><th>Arrival</th><th>Seats</th><th>Status</th></tr></thead><tbody>
<?php foreach ($trips as $t): ?><tr><td><strong><?= e($t['origin']) ?></strong> → <?= e($t['destination']) ?></td><td><?= e($t['operator_name']) ?><div class="muted small"><?= e($t['vehicle_type']) ?> · <?= e($t['plate_number']) ?></div></td><td><?= e(date('M j, g:i A', strtotime($t['departure_time']))) ?></td><td><?= e(date('M j, g:i A', strtotime($t['arrival_time']))) ?></td><td><?= (int)$t['seats_available'] ?></td><td><span class="status"><?= e(ucfirst($t['status'])) ?></span></td></tr><?php endforeach; ?>
</tbody></table></div><?php else: ?><p class="empty">No scheduled trips found.</p><?php endif; ?></section>
<section class="notice"><strong>Operations note</strong><p>This starter provides a read-only schedule for staff. Dispatch, trip editing, and check-in workflows can be added to match your terminal's process.</p></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
