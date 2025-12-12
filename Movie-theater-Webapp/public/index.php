<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

// Read filters from query string
$searchTitle    = trim($_GET['q'] ?? '');
$selectedRating = trim($_GET['rating'] ?? '');

// Load list of distinct MPAA ratings for the dropdown
$ratingOptions = [];
try {
    $ratingStmt = $pdo->query("
        SELECT DISTINCT mpaaRating AS rating
        FROM Movie
        WHERE mpaaRating IS NOT NULL AND mpaaRating <> ''
        ORDER BY rating
    ");
    $ratingOptions = $ratingStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $ratingOptions = [];
}

// Build movie query with optional filters
$sql = "
    SELECT
        movie_ID,
        title,
        mpaaRating AS rating,
        runtime    AS runtimeMinutes,
        releaseDate
    FROM Movie
    WHERE 1 = 1
";

$params = [];

// Filter by rating if selected
if ($selectedRating !== '') {
    $sql .= " AND mpaaRating = :rating ";
    $params[':rating'] = $selectedRating;
}

// Filter by title search if provided
if ($searchTitle !== '') {
    $sql .= " AND title LIKE :title ";
    $params[':title'] = '%' . $searchTitle . '%';
}

$sql .= " ORDER BY title";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movies = $stmt->fetchAll();

/**
 * Poster mapping:
 * Put your image files in: public/assets/img/
 * Update these filenames to match what you have.
 */
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
];

$postersToShow = [];

foreach ($movies as $m) {
    $title = $m['title'] ?? '';
    if (isset($posterMapByTitle[$title])) {
        $postersToShow[] = [
            'title' => $title,
            'movie_ID' => $m['movie_ID'],
            'file' => $posterMapByTitle[$title],
        ];
    }
}
// Build poster list from the movies currently shown (after filters)

?>

<h2>Browse Movies</h2>

<form method="get" action="" style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end; margin-bottom:1rem;">
    <div>
        <label for="q">Title has :</label><br>
        <input
            type="text"
            id="q"
            name="q"
            placeholder="e.g. dark, toy, spider"
            value="<?= htmlspecialchars($searchTitle) ?>"
        >
    </div>

    <div>
        <label for="rating">Rating:</label><br>
        <select name="rating" id="rating">
            <option value="">All ratings</option>
            <?php foreach ($ratingOptions as $r): ?>
                <option value="<?= htmlspecialchars($r) ?>"
                    <?= $selectedRating === $r ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <button type="submit">Filter</button>
    </div>
</form>

<div class="movies-layout">
    <div>
        <?php if (empty($movies)): ?>
            <p>No movies found matching your filters.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Rating</th>
                        <th>Runtime (min)</th>
                        <th>Release Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movies as $movie): ?>
                        <tr>
                            <td>
                                <a href="movie.php?id=<?= urlencode($movie['movie_ID']) ?>">
                                    <?= htmlspecialchars($movie['title']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($movie['rating']) ?></td>
                            <td><?= htmlspecialchars($movie['runtimeMinutes']) ?></td>
                            <td><?= htmlspecialchars($movie['releaseDate']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php if (!empty($postersToShow)): ?>
    <aside class="poster-panel">
    <h3>Poster</h3>

    <div class="poster-grid">
        <?php foreach ($postersToShow as $p): ?>
            <img
                src="assets/img/posters/<?= htmlspecialchars($p['file']) ?>"
                alt="<?= htmlspecialchars($p['title']) ?>"
                loading="lazy"
            >
        <?php endforeach; ?>
    </div>
</aside>
<?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
