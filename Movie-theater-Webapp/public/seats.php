<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

// 1) Get and validate showtime_id from ?showtime_id=
$showtimeId = get_int_from_get('showtime_id');
if ($showtimeId === null) {
    redirect('showtimes.php');
}

// 2) Fetch showtime details (movie, theatre, auditorium)
$sqlShowtime = "
    SELECT 
        s.showtime_ID,
        s.showDateTime,
        s.format,
        s.language,
        m.title              AS movie_title,
        a.auditorium_ID,
        a.name               AS auditorium_name,
        t.name               AS theatre_name
    FROM Showtime s
    JOIN Movie      m ON s.movie_ID      = m.movie_ID
    JOIN Auditorium a ON s.auditorium_ID = a.auditorium_ID
    JOIN Theatre    t ON a.theatre_ID    = t.theatre_ID
    WHERE s.showtime_ID = :showtime_id
";
$stmt = $pdo->prepare($sqlShowtime);
$stmt->execute([':showtime_id' => $showtimeId]);
$showtime = $stmt->fetch();

if (!$showtime) {
    redirect('showtimes.php');
}

$auditoriumId = (int)$showtime['auditorium_ID'];

// 3) Fetch all seats for that auditorium, and see which are sold for this showtime
$sqlSeats = "
    SELECT 
        seat.seat_ID,
        seat.row_label,
        seat.seatNumber,
        seat.seatType,
        CASE 
            WHEN t.ticket_ID IS NULL THEN 0 
            ELSE 1 
        END AS is_sold
    FROM Seat seat
    JOIN Auditorium a ON seat.auditorium_ID = a.auditorium_ID
    LEFT JOIN Ticket t 
        ON t.seat_ID = seat.seat_ID
       AND t.showtime_ID = :showtime_id
       AND t.status = 'ACTIVE'
    WHERE seat.auditorium_ID = :auditorium_id
    ORDER BY seat.row_label, seat.seatNumber
";
$stmt2 = $pdo->prepare($sqlSeats);
$stmt2->execute([
    ':showtime_id'   => $showtimeId,
    ':auditorium_id' => $auditoriumId,
]);
$seatRows = $stmt2->fetchAll();

// 4) Organize seats into a grid: [row_label => [ seat, seat, ... ]]
$seatGrid = [];
foreach ($seatRows as $seat) {
    $rowLabel = $seat['row_label'];
    if (!isset($seatGrid[$rowLabel])) {
        $seatGrid[$rowLabel] = [];
    }
    $seatGrid[$rowLabel][] = $seat;
}
?>

<h2>Select Seats</h2>

<p>
    <strong>Movie:</strong> <?= htmlspecialchars($showtime['movie_title']) ?><br>
    <strong>Theatre:</strong> <?= htmlspecialchars($showtime['theatre_name']) ?><br>
    <strong>Auditorium:</strong> <?= htmlspecialchars($showtime['auditorium_name']) ?><br>
    <strong>Date &amp; Time:</strong> <?= htmlspecialchars($showtime['showDateTime']) ?><br>
    <strong>Format:</strong> <?= htmlspecialchars($showtime['format']) ?>,
    <strong>Language:</strong> <?= htmlspecialchars($showtime['language']) ?>
</p>

<h3>Seat Map</h3>

<?php if (empty($seatGrid)): ?>
    <p>No seats found for this auditorium.</p>
<?php else: ?>

    <div class="seat-legend">
        <span class="seat seat-available">A</span> Available
        <span class="seat seat-sold">X</span> Sold
    </div>

    <div class="seat-grid-wrapper">
        <div class="seat-screen">SCREEN</div>

        <div class="seat-grid">
            <?php foreach ($seatGrid as $rowLabel => $seats): ?>
                <div class="seat-row">
                    <span class="seat-row-label"><?= htmlspecialchars($rowLabel) ?></span>
                    <?php foreach ($seats as $seat): ?>
                        <?php if ((int)$seat['is_sold'] === 1): ?>
                            <span class="seat seat-sold">
                                <?= htmlspecialchars($seat['seatNumber']) ?>
                            </span>
                        <?php else: ?>
                            <a class="seat seat-available"
                               href="purchase.php?showtime_id=<?= urlencode($showtimeId) ?>&seat_id=<?= urlencode($seat['seat_ID']) ?>">
                                <?= htmlspecialchars($seat['seatNumber']) ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php endif; ?>

<p>
    <a href="showtimes.php">&larr; Back to Showtimes</a>
</p>

<?php
require_once __DIR__ . '/../includes/footer.php';
