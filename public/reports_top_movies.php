<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

// Read optional date range from query string
$startDate = trim($_GET['start_date'] ?? '');
$endDate   = trim($_GET['end_date'] ?? '');

$error = null;
$rows  = [];

try {
    $sql = "
        SELECT 
            title,
            SUM(tickets_sold) AS total_tickets
        FROM v_movie_ticket_sales
        WHERE 1 = 1
    ";

    $params = [];

    // If user chose a start date, filter by it
    if ($startDate !== '') {
        $sql .= " AND show_date >= :start_date ";
        $params[':start_date'] = $startDate;
    }

    // If user chose an end date, filter by it
    if ($endDate !== '') {
        $sql .= " AND show_date <= :end_date ";
        $params[':end_date'] = $endDate;
    }

    $sql .= "
        GROUP BY title
        ORDER BY total_tickets DESC, title ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error loading top movies: " . htmlspecialchars($e->getMessage());
}
?>

<h2>Top Movies by Tickets Sold</h2>

<p>
    This report shows how many tickets have been sold for each movie.
    Use the optional date range filters to narrow down the results.
</p>

<form method="get" action="" style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end; margin-bottom:1rem;">
    <div>
        <label for="start_date">Start date (optional):</label><br>
        <input type="date" id="start_date" name="start_date"
               value="<?= htmlspecialchars($startDate) ?>">
    </div>

    <div>
        <label for="end_date">End date (optional):</label><br>
        <input type="date" id="end_date" name="end_date"
               value="<?= htmlspecialchars($endDate) ?>">
    </div>

    <div>
        <button type="submit">Run Report</button>
    </div>
</form>

<?php if ($error): ?>
    <div style="background:#5a1a1a; color:white; padding:0.75rem; border-radius:4px; margin-bottom:1rem;">
        <?= $error ?>
    </div>
<?php endif; ?>

<?php if (!$error): ?>

    <?php if (empty($rows)): ?>
        <p>No ticket sales found for the selected date range.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Movie</th>
                    <th>Total Tickets Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['title']) ?></td>
                        <td><?= htmlspecialchars($r['total_tickets']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php endif; ?>

<?php
require_once __DIR__ . '/../includes/footer.php';
