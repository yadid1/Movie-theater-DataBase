<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

$error = null;
$rows  = [];
$theatres = [];
$selectedTheatreId = get_int_from_get('theatre_id');

// Load distinct theatres that appear in the utilization view
try {
    $theatreStmt = $pdo->query("
        SELECT DISTINCT theatre_ID, theatre_name
        FROM v_theatre_utilization_next_7_days
        ORDER BY theatre_name
    ");
    $theatres = $theatreStmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error loading utilization data: " . htmlspecialchars($e->getMessage());
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
                sold_seats,
                utilization_percent
            FROM v_theatre_utilization_next_7_days
            WHERE 1 = 1
        ";

        $params = [];

        if ($selectedTheatreId !== null) {
            $sql .= " AND theatre_ID = :theatre_id ";
            $params[':theatre_id'] = $selectedTheatreId;
        }

        $sql .= "
            ORDER BY
                theatre_name,
                auditorium_name,
                showDateTime,
                movie_title
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Error loading utilization data: " . htmlspecialchars($e->getMessage());
    }
}
?>

<h2>Theatre Utilization (Next 7 Days)</h2>

<p>
    This report shows seat utilization for each showtime occurring in the next seven days.
    Utilization is calculated as the percentage of seats sold in each auditorium for a given showtime.
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
        <p>No upcoming showtimes were found in the next seven days.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Theatre</th>
                    <th>Auditorium</th>
                    <th>Movie</th>
                    <th>Date &amp; Time</th>
                    <th>Total Seats</th>
                    <th>Seats Sold</th>
                    <th>Utilization</th>
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
                        <td><?= htmlspecialchars($r['sold_seats']) ?></td>
                        <td><?= htmlspecialchars($r['utilization_percent']) ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php endif; ?>

<?php
require_once __DIR__ . '/../includes/footer.php';
