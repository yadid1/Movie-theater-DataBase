<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

// 1) Fetch all theatres for the dropdown
$theatreStmt = $pdo->query("SELECT theatre_ID, name FROM Theatre ORDER BY name");
$theatres = $theatreStmt->fetchAll();

if (empty($theatres)) {
    echo "<p>No theatres found.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// 2) Determine selected theatre and date (date is OPTIONAL)
$selectedTheatreId = get_int_from_get('theatre_id');
if ($selectedTheatreId === null) {
    $selectedTheatreId = (int)$theatres[0]['theatre_ID']; // default to first theatre
}

$selectedDate = trim($_GET['show_date'] ?? ''); // format yyyy-mm-dd or empty string

// 3) Load showtimes for the selected theatre (and optional date)
$sql = "
    SELECT 
        s.showtime_ID,
        s.showDateTime,
        s.format,
        s.language,
        m.title         AS movie_title,
        a.name          AS auditorium_name
    FROM Showtime s
    JOIN Movie      m ON s.movie_ID      = m.movie_ID
    JOIN Auditorium a ON s.auditorium_ID = a.auditorium_ID
    WHERE a.theatre_ID = :theatre_id
";

$params = [
    ':theatre_id' => $selectedTheatreId,
];

if ($selectedDate !== '') {
    // Filter to a single calendar day if user picked one
    $sql .= " AND DATE(s.showDateTime) = :show_date ";
    $params[':show_date'] = $selectedDate;
}

$sql .= " ORDER BY s.showDateTime, m.title";

$showStmt = $pdo->prepare($sql);
$showStmt->execute($params);
$showtimes = $showStmt->fetchAll();

// 4) Group showtimes by calendar date for a "calendar list" feel
$showtimesByDate = [];
foreach ($showtimes as $row) {
    $date = substr($row['showDateTime'], 0, 10); // YYYY-MM-DD
    if (!isset($showtimesByDate[$date])) {
        $showtimesByDate[$date] = [];
    }
    $showtimesByDate[$date][] = $row;
}
?>

<h2>Find Showtimes</h2>

<p>
    Select a theatre and (optionally) a date.  
    If you leave the date blank, all upcoming showtimes for that theatre will be listed.
</p>

<form method="get" action="" style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end; margin-bottom:1rem;">
    <div>
        <label for="theatre_id">Theatre:</label><br>
        <select name="theatre_id" id="theatre_id">
            <?php foreach ($theatres as $theatre): ?>
                <option value="<?= htmlspecialchars($theatre['theatre_ID']) ?>"
                    <?= $selectedTheatreId === (int)$theatre['theatre_ID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($theatre['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="show_date">Date (optional):</label><br>
        <input type="date" name="show_date" id="show_date"
               value="<?= htmlspecialchars($selectedDate) ?>">
    </div>

    <div>
        <button type="submit">Search</button>
    </div>
</form>

<?php if (empty($showtimes)): ?>

    <?php if ($selectedDate !== ''): ?>
        <p>No showtimes found for this theatre on <strong><?= htmlspecialchars($selectedDate) ?></strong>.</p>
    <?php else: ?>
        <p>No showtimes found for this theatre.</p>
    <?php endif; ?>

<?php else: ?>

    <?php if ($selectedDate !== ''): ?>
        <h3>Showtimes on <?= htmlspecialchars($selectedDate) ?></h3>
    <?php else: ?>
        <h3>Upcoming Showtimes</h3>
    <?php endif; ?>

    <?php foreach ($showtimesByDate as $date => $rows): ?>
        <h4 style="margin-top:1.25rem;"><?= htmlspecialchars($date) ?></h4>
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Movie</th>
                    <th>Auditorium</th>
                    <th>Format</th>
                    <th>Language</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $st): ?>
                    <tr>
                        <td><?= htmlspecialchars(substr($st['showDateTime'], 11)) ?></td>
                        <td><?= htmlspecialchars($st['movie_title']) ?></td>
                        <td><?= htmlspecialchars($st['auditorium_name']) ?></td>
                        <td><?= htmlspecialchars($st['format']) ?></td>
                        <td><?= htmlspecialchars($st['language']) ?></td>
                        <td>
                            <a href="seats.php?showtime_id=<?= urlencode($st['showtime_ID']) ?>">
                                View Seats
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

<?php endif; ?>

<?php
require_once __DIR__ . '/../includes/footer.php';
