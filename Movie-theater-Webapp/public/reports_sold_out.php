<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

$error = null;
$rows  = [];
$theatres = [];
$selectedTheatreId = get_int_from_get('theatre_id');

// Load distinct theatres that have at least one sold-out showtime
try {
    $theatreStmt = $pdo->query("
        SELECT DISTINCT theatre_ID, theatre_name
        FROM v_sold_out_showtimes
        ORDER BY theatre_name
    ");
    $theatres = $theatreStmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error loading sold-out showtimes: " . htmlspecialchars($e->getMessage());
}

if (!$error) {
    try {
        $sql = "
            SELECT
                theatre_ID,
                theatre_name,
                auditorium_ID,
                auditorium_name,
                showtime_ID,
                movie_title,
                showDateTime,
                total_seats,
                active_tickets
            FROM v_sold_out_showtimes
            WHERE 1 = 1
        ";

        $params = [];

        if ($selectedTheatreId !== null) {
            $sql .= " AND theatre_ID = :theatre_id ";
            $params[':theatre_id'] = $selectedTheatreId;
        }

        $sql .= " ORDER BY theatre_name, showDateTime, movie_title ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Error loading sold-out showtimes: " . htmlspecialchars($e->getMessage());
    }
}
?>

<h2>Sold-Out Showtimes</h2>

<p>
    This report lists upcoming showtimes that are fully sold out.
    Use the theatre filter to narrow down the results.
</p>

<?php if (!empty($theatres)): ?>
    <form method="get" action="" style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end; margin-bottom:1rem;">
        <div>
            <label for="theatre_id">Theatre (optional):</label><br>
            <select name="theatre_id" id="theatre_id">
                <option value="">All theatres</option>
                <?php foreach ($theatres as $th): ?>
                    <option value="<?= htmlspecialchars($th['theatre_ID']) ?>"
                        <?= ($selectedTheatreId !== null && $selectedTheatreId === (int)$th['theatre_ID']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($th['theatre_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit">Run Report</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($error): ?>
    <div style="background:#5a1a1a; color:white; padding:0.75rem; border-radius:4px; margin-bottom:1rem;">
        <?= $error ?>
    </div>
<?php endif; ?>

<?php if (!$error): ?>

    <?php if (empty($rows)): ?>
        <p>No upcoming sold-out showtimes were found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Theatre</th>
                    <th>Auditorium</th>
                    <th>Movie</th>
                    <th>Date &amp; Time</th>
                    <th>Total Seats</th>
                    <th>Tickets Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['theatre_name']) ?></td>
                        <td><?= htmlspecialchars($r['auditorium_name']) ?></td>
                        <td><?= htmlspecialchars($r['movie_title']) ?></td>
                        <td><?= htmlspecialchars($r['showDateTime']) ?></td>
                        <td><?= htmlspecialchars($r['total_seats']) ?></td>
                        <td><?= htmlspecialchars($r['active_tickets']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php endif; ?>

<?php
require_once __DIR__ . '/../includes/footer.php';
