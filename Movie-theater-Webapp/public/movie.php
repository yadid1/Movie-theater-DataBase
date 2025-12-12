<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

// 1) Get and validate movie ID from ?id=
$movieId = get_int_from_get('id');
if ($movieId === null) {
    // no or invalid id => go back to movies list
    redirect('index.php');
}

// 2) Fetch movie info
$sqlMovie = "
    SELECT movie_ID, title, mpaaRating, runtime,
           DATE_FORMAT(releaseDate, '%Y-%m-%d') AS releaseDate
    FROM Movie
    WHERE movie_ID = :id
";
$stmt = $pdo->prepare($sqlMovie);
$stmt->execute([':id' => $movieId]);
$movie = $stmt->fetch();

if (!$movie) {
    // movie not found -> go back to list
    redirect('index.php');
}

// 3) Fetch upcoming showtimes for this movie
$sqlShowtimes = "
    SELECT 
        s.showtime_ID,
        s.format,
        s.language,
        s.showDateTime,
        t.name   AS theatre_name,
        a.name   AS auditorium_name
    FROM Showtime s
    JOIN Auditorium a ON s.auditorium_ID = a.auditorium_ID
    JOIN Theatre   t ON a.theatre_ID     = t.theatre_ID
    WHERE s.movie_ID = :movie_id
      AND s.showDateTime >= NOW()
    ORDER BY s.showDateTime ASC
";
$stmt2 = $pdo->prepare($sqlShowtimes);
$stmt2->execute([':movie_id' => $movieId]);
$showtimes = $stmt2->fetchAll();
?>

<h2><?= htmlspecialchars($movie['title']) ?></h2>

<p>
    <strong>Rating:</strong> <?= htmlspecialchars($movie['mpaaRating']) ?><br>
    <strong>Runtime:</strong> <?= htmlspecialchars($movie['runtime']) ?> minutes<br>
    <strong>Release Date:</strong> <?= htmlspecialchars($movie['releaseDate']) ?>
</p>

<h3>Upcoming Showtimes</h3>

<?php if (empty($showtimes)): ?>
    <p>No upcoming showtimes for this movie.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Date &amp; Time</th>
                <th>Theatre</th>
                <th>Auditorium</th>
                <th>Format</th>
                <th>Language</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($showtimes as $st): ?>
            <tr>
                <td><?= htmlspecialchars($st['showDateTime']) ?></td>
                <td><?= htmlspecialchars($st['theatre_name']) ?></td>
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
<?php endif; ?>

<p>
    <a href="index.php">&larr; Back to Movies</a>
</p>

<?php
require_once __DIR__ . '/../includes/footer.php';
