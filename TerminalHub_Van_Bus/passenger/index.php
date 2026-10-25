<?php
require_once __DIR__ . '/../includes/auth.php';
$user = require_role('passenger','admin');
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trip_id'])) {
    $tripId = filter_var($_POST['trip_id'], FILTER_VALIDATE_INT);
    $seats = filter_var($_POST['seats'] ?? 1, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>8]]);
    if (!$tripId || !$seats) {
        $error = 'Choose a valid trip and between 1 and 8 seats.';
    } else {
        try {
            $pdo = db();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("SELECT id,seats_available,status,departure_time FROM trips WHERE id=? FOR UPDATE");
            $stmt->execute([$tripId]);
            $trip = $stmt->fetch();
            if (!$trip || $trip['status'] !== 'scheduled' || strtotime($trip['departure_time']) <= time()) {
                $error = 'This trip is no longer available for booking.';
            } elseif ((int)$trip['seats_available'] < $seats) {
                $error = 'Not enough seats remain for this trip.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO bookings (user_id,trip_id,seats,status) VALUES (?,?,?,'pending')");
                $stmt->execute([$user['id'],$tripId,$seats]);
                $message = 'Booking request submitted. Your request is pending confirmation.';
            }
            $pdo->commit();
        } catch (PDOException $ex) {
            if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
            $error = 'Could not submit the request. Please check the database and try again.';
        }
    }
}
try {
  $trips = db()->query("SELECT t.id,t.departure_time,t.arrival_time,t.fare,t.seats_available,t.status,r.origin,r.destination,v.operator_name,v.vehicle_type FROM trips t JOIN routes r ON r.id=t.route_id JOIN vehicles v ON v.id=t.vehicle_id WHERE t.status='scheduled' AND t.departure_time > NOW() ORDER BY t.departure_time LIMIT 50")->fetchAll();
  $stmt = db()->prepare('SELECT b.id,b.seats,b.status,b.created_at,t.departure_time,r.origin,r.destination FROM bookings b JOIN trips t ON t.id=b.trip_id JOIN routes r ON r.id=t.route_id WHERE b.user_id=? ORDER BY b.created_at DESC LIMIT 10');
  $stmt->execute([$user['id']]); $bookings = $stmt->fetchAll();
} catch (PDOException $ex) { $trips = []; $bookings = []; $error = 'Database tables are not ready. Import database/terminalhub.sql.'; }
$title = 'Passenger dashboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-heading"><div><div class="eyebrow">PLAN YOUR JOURNEY</div><h1>Passenger dashboard</h1><p class="muted">Browse scheduled van and bus departures.</p></div><span class="role-pill">PASSENGER</span></div>
<?php if ($message): ?><div class="success"><?= e($message) ?></div><?php endif; ?><?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<section class="trip-grid">
<?php foreach ($trips as $t): ?><article class="trip-card"><div class="trip-top"><span class="status">Scheduled</span><span class="fare">₱<?= number_format((float)$t['fare'],2) ?></span></div><div class="trip-route"><div><small>FROM</small><strong><?= e($t['origin']) ?></strong></div><span class="route-arrow">→</span><div><small>TO</small><strong><?= e($t['destination']) ?></strong></div></div><p class="muted"><?= e($t['operator_name']) ?> · <?= e($t['vehicle_type']) ?></p><p class="trip-time"><?= e(date('D, M j · g:i A', strtotime($t['departure_time']))) ?></p><p class="muted small"><?= (int)$t['seats_available'] ?> seats currently listed</p>
<form method="post" class="booking-form"><input type="hidden" name="trip_id" value="<?= (int)$t['id'] ?>"><label>Seats<select name="seats"><?php for ($i=1;$i<=min(8,(int)$t['seats_available']);$i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?></select></label><button class="button" type="submit" <?= (int)$t['seats_available'] < 1 ? 'disabled' : '' ?>>Request booking</button></form></article><?php endforeach; ?>
</section>
<?php if (!$trips): ?><section class="panel"><p class="empty">No upcoming trips are currently published. Please check again later.</p></section><?php endif; ?>
<section class="panel"><div class="panel-head"><h2>My booking requests</h2></div>
<?php if ($bookings): ?><div class="table-wrap"><table><thead><tr><th>Route</th><th>Departure</th><th>Seats</th><th>Status</th><th>Requested</th></tr></thead><tbody><?php foreach ($bookings as $b): ?><tr><td><?= e($b['origin']) ?> → <?= e($b['destination']) ?></td><td><?= e(date('M j, Y g:i A', strtotime($b['departure_time']))) ?></td><td><?= (int)$b['seats'] ?></td><td><span class="status"><?= e(ucfirst($b['status'])) ?></span></td><td><?= e(date('M j, Y', strtotime($b['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><p class="empty">You haven't submitted any booking requests yet.</p><?php endif; ?></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
