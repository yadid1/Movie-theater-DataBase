<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

// Read email from query string (?email=...)
$email = trim($_GET['email'] ?? '');
$error = null;
$tickets = [];

if ($email !== '') {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Look up tickets for this email
        $sql = "
            SELECT 
                t.ticket_ID,
                t.price,
                t.status,
                s.showDateTime,
                m.title                AS movie_title,
                th.name                AS theatre_name,
                a.name                 AS auditorium_name,
                seat.row_label,
                seat.seatNumber
            FROM Ticket t
            JOIN Customer c   ON t.customer_ID   = c.customer_ID
            JOIN Showtime s   ON t.showtime_ID   = s.showtime_ID
            JOIN Movie m      ON s.movie_ID      = m.movie_ID
            JOIN Auditorium a ON s.auditorium_ID = a.auditorium_ID
            JOIN Theatre th   ON a.theatre_ID    = th.theatre_ID
            JOIN Seat seat    ON t.seat_ID       = seat.seat_ID
            WHERE c.email = :email
            ORDER BY s.showDateTime, t.ticket_ID
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $tickets = $stmt->fetchAll();
    }
}
?>

<h2>My Tickets</h2>

<p>Enter the email you used when purchasing your tickets.</p>

<form method="get" action="" style="max-width: 420px; margin-bottom: 1rem;">
    <div style="margin-bottom: 0.5rem;">
        <label>
            Email:<br>
            <input type="email" name="email" required style="width: 100%;"
                   value="<?= htmlspecialchars($email) ?>">
        </label>
    </div>
    <button type="submit">Find Tickets</button>
</form>

<?php if ($error): ?>
    <div style="padding: 0.75rem; background: #5a1a1a; color: #fff; margin-bottom: 1rem; border-radius: 4px;">
        <?= $error ?>
    </div>
<?php endif; ?>

<?php if ($email !== '' && !$error): ?>

    <?php if (empty($tickets)): ?>
        <p>No tickets found for <strong><?= htmlspecialchars($email) ?></strong>.</p>
    <?php else: ?>
        <p>Showing tickets for <strong><?= htmlspecialchars($email) ?></strong>:</p>

        <table>
            <thead>
                <tr>
                    <th>Ticket #</th>
                    <th>Movie</th>
                    <th>Theatre</th>
                    <th>Auditorium</th>
                    <th>Date &amp; Time</th>
                    <th>Seat</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['ticket_ID']) ?></td>
                        <td><?= htmlspecialchars($t['movie_title']) ?></td>
                        <td><?= htmlspecialchars($t['theatre_name']) ?></td>
                        <td><?= htmlspecialchars($t['auditorium_name']) ?></td>
                        <td><?= htmlspecialchars($t['showDateTime']) ?></td>
                        <td>
                            Row <?= htmlspecialchars($t['row_label']) ?>
                            Seat <?= htmlspecialchars($t['seatNumber']) ?>
                        </td>
                        <td>$<?= htmlspecialchars($t['price']) ?></td>
                        <td><?= htmlspecialchars($t['status']) ?></td>
                        <td>
                            <?php if ($t['status'] === 'ACTIVE'): ?>
                                <a href="refund.php?ticket=<?= urlencode($t['ticket_ID']) ?>"
                                   style="color: #ff8080; text-decoration: underline;">
                                    Refund
                                </a>
                            <?php else: ?>
                                &mdash;
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php endif; ?>

<?php
require_once __DIR__ . '/../includes/footer.php';
