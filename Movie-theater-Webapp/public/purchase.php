<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

// Read showtime & seat from query string
$showtimeId = get_int_from_get('showtime_id');
$seatId     = get_int_from_get('seat_id');

if ($showtimeId === null || $seatId === null) {
    redirect('showtimes.php');
}

$error    = null;
$success  = null;
$ticketId = null;

// 1) Fetch showtime + seat details for display
$sqlInfo = "
    SELECT 
        s.showtime_ID,
        s.showDateTime,
        s.format,
        s.language,
        m.title              AS movie_title,
        a.auditorium_ID,
        a.name               AS auditorium_name,
        t.name               AS theatre_name,
        seat.row_label,
        seat.seatNumber,
        seat.seatType
    FROM Showtime s
    JOIN Movie      m   ON s.movie_ID      = m.movie_ID
    JOIN Auditorium a   ON s.auditorium_ID = a.auditorium_ID
    JOIN Theatre    t   ON a.theatre_ID    = t.theatre_ID
    JOIN Seat       seat 
         ON seat.seat_ID        = :seat_id
        AND seat.auditorium_ID  = a.auditorium_ID
    WHERE s.showtime_ID = :showtime_id
";
$stmt = $pdo->prepare($sqlInfo);
$stmt->execute([
    ':showtime_id' => $showtimeId,
    ':seat_id'     => $seatId,
]);
$info = $stmt->fetch();

if (!$info) {
    // invalid combo → back to showtimes
    redirect('showtimes.php');
}

// 2) Handle form submission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_validate();

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name']  ?? '');
        $email     = trim($_POST['email']      ?? '');
        $phone     = trim($_POST['phone']      ?? '');
        $discount  = trim($_POST['discount_code'] ?? '');

        if ($firstName === '' || $lastName === '' || $email === '') {
            throw new Exception('First name, last name, and email are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address.');
        }

        $pdo->beginTransaction();

        // 2a) Find or create customer by email
        $customerId = null;

        $stmt = $pdo->prepare("SELECT customer_ID FROM Customer WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $existing = $stmt->fetch();

        if ($existing) {
            $customerId = (int)$existing['customer_ID'];
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO Customer (firstName, lastName, email, phoneNumber)
                VALUES (:first_name, :last_name, :email, :phone)
            ");
            $stmt->execute([
                ':first_name' => $firstName,
                ':last_name'  => $lastName,
                ':email'      => $email,
                ':phone'      => $phone !== '' ? $phone : null,
            ]);
            $customerId = (int)$pdo->lastInsertId();
        }

        // 2b) Call stored procedure sell_ticket
        // signature (from Part B):
        // sell_ticket(IN p_showtime_id, IN p_seat_id, IN p_customer_id,
        //             IN p_discount_code, OUT p_ticket_id)
        $discountParam = $discount !== '' ? $discount : null;

        $call = $pdo->prepare(
            "CALL sell_ticket(:showtime_id, :seat_id, :customer_id, :discount_code, @new_ticket_id)"
        );
        $call->execute([
            ':showtime_id'   => $showtimeId,
            ':seat_id'       => $seatId,
            ':customer_id'   => $customerId,
            ':discount_code' => $discountParam,
        ]);
        $call->closeCursor(); // important before running another query

        // Retrieve OUT parameter
        $result = $pdo->query("SELECT @new_ticket_id AS ticket_id");
        $row    = $result->fetch();
        $ticketId = $row['ticket_id'] ?? null;

        $pdo->commit();

        if ($ticketId) {
            $success = "Ticket purchased successfully! Your ticket ID is #{$ticketId}.";
        } else {
            $error = 'Purchase completed, but could not retrieve ticket ID.';
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Stored procedure uses SIGNAL for business rules (e.g., seat already taken)
        $error = 'Could not complete purchase: ' . htmlspecialchars($e->getMessage());
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}
?>

<h2>Purchase Ticket</h2>

<p>
    <strong>Movie:</strong> <?= htmlspecialchars($info['movie_title']) ?><br>
    <strong>Theatre:</strong> <?= htmlspecialchars($info['theatre_name']) ?><br>
    <strong>Auditorium:</strong> <?= htmlspecialchars($info['auditorium_name']) ?><br>
    <strong>Date &amp; Time:</strong> <?= htmlspecialchars($info['showDateTime']) ?><br>
    <strong>Format:</strong> <?= htmlspecialchars($info['format']) ?>,
    <strong>Language:</strong> <?= htmlspecialchars($info['language']) ?><br>
    <strong>Seat:</strong> Row <?= htmlspecialchars($info['row_label']) ?>
    Seat <?= htmlspecialchars($info['seatNumber']) ?>
    (<?= htmlspecialchars($info['seatType']) ?>)
</p>

<?php if ($error): ?>
    <div style="padding: 0.75rem; background: #5a1a1a; color: #fff; margin-bottom: 1rem; border-radius: 4px;">
        <?= $error ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="padding: 0.75rem; background: #145a32; color: #fff; margin-bottom: 1rem; border-radius: 4px;">
        <?= $success ?>
    </div>
    <p>
        You can view your tickets later on the <a href="tickets.php">My Tickets</a> page using your email.
    </p>
<?php else: ?>

    <form method="post" action="" style="max-width: 420px;">
        <?php csrf_field(); ?>

        <div style="margin-bottom: 0.5rem;">
            <label>
                First Name:<br>
                <input type="text" name="first_name" required style="width: 100%;">
            </label>
        </div>

        <div style="margin-bottom: 0.5rem;">
            <label>
                Last Name:<br>
                <input type="text" name="last_name" required style="width: 100%;">
            </label>
        </div>

        <div style="margin-bottom: 0.5rem;">
            <label>
                Email:<br>
                <input type="email" name="email" required style="width: 100%;">
            </label>
        </div>

        <div style="margin-bottom: 0.5rem;">
            <label>
                Phone (optional):<br>
                <input type="text" name="phone" style="width: 100%;">
            </label>
        </div>

        <div style="margin-bottom: 0.75rem;">
            <label>
                Discount Code (optional):<br>
                <input type="text" name="discount_code" style="width: 100%;" placeholder="e.g., STUDENT10">
            </label>
        </div>

        <button type="submit">Confirm Purchase</button>
    </form>

<?php endif; ?>

<p style="margin-top: 1.5rem;">
    <a href="seats.php?showtime_id=<?= urlencode($showtimeId) ?>">&larr; Back to Seats</a>
</p>

<?php
require_once __DIR__ . '/../includes/footer.php';
