<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

// 1) Get and validate movie ID
$movieId = get_int_from_get('id');
if ($movieId === null) {
    redirect('index.php');
}

// 2) Fetch movie info
$sqlMovie = "
    SELECT
        movie_ID,
        title,
        mpaaRating,
        runtime,
        DATE_FORMAT(releaseDate, '%Y-%m-%d') AS releaseDate
    FROM Movie
    WHERE movie_ID = :id
";
$stmt = $pdo->prepare($sqlMovie);
$stmt->execute([':id' => $movieId]);
$movie = $stmt->fetch();

if (!$movie) {
    redirect('index.php');
}

// 3) Poster mapping (SAME AS index.php)
$posterMapByTitle = [
    'Avatar' => 'avatar.jpg',
    'Dune' => 'dune.jpg',
    'Frozen' => 'frozen.jpg',
    'Inception' => 'inception.jpg',
    'Interstellar' => 'interstellar.jpg',
    'Oppenheimer' => 'oppenheimer.jpg',
    'Spider-Man: Across the Spider-Verse' => 'spiderverse.jpg',
    'The Batman' => 'the_batman.jpg',
    'The Dark Knight' => 'dark_knight.jpg',
    'The Matrix' => 'matrix.jpg',
    'Top Gun: Maverick' => 'top_gun.jpg',
    'Toy Story 4' => 'toy_story_4.jpg',
];

// Resolve poster path
$title = $movie['title'];
if (isset($posterMapByTitle[$title])) {
    $posterRel = "assets/img/posters/" . $posterMapByTitle[$title];
} else {
    $posterRel = "assets/img/posters/placeholder.jpg";
}

// 4) Fetch upcoming showtimes
$sqlShowtimes = "
    SELECT 
        s.showtime_ID,
        s.format,
        s.language,
        s.showDateTime,
        t.name AS theatre_name,
        a.name AS auditorium_name
    FROM Showtime s
    JOIN Auditorium a ON s.auditorium_ID = a.auditorium_ID
    JOIN Theatre t ON a.theatre_ID = t.theatre_ID
    WHERE s.movie_ID = :movie_id
      AND s.showDateTime >= NOW()
    ORDER BY s.showDateTime ASC
";
$stmt2 = $pdo->prepare($sqlShowtimes);
$stmt2->execute([':movie_id' => $movieId]);
$showtimes = $stmt2->fetchAll();
?>

<div class="movies-layout">

    <!-- LEFT: Movie details + showtimes -->
    <div>
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
    </div>

    <!-- RIGHT: Poster panel -->
    <aside class="poster-panel">
        <h3>Poster</h3>

        <div class="poster-grid">
            <img
                src="<?= htmlspecialchars($posterRel) ?>"
                alt="<?= htmlspecialchars($movie['title']) ?> poster"
                loading="lazy"
            >
        </div>
    </aside>

</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
