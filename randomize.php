<?php
include 'index.php';

// Fetch genres for dropdown
$stmt = $pdo->query("SELECT Genre FROM genres");
$genres = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Randomize Book</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Randomize by Genre</h1>

<form method="post">
    <label for="genre">Choose a genre:</label>
    <select name="genre" id="genre">
        <?php foreach($genres as $genre): ?>
            <option value="<?php echo htmlspecialchars($genre); ?>"><?php echo htmlspecialchars($genre); ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Randomize</button>
</form>

<?php
if (isset($_POST['genre']) && $_POST['genre'] !== "") {
    $genre = $_POST['genre'];
    $stmt = $pdo->prepare("SELECT Genre, BookTitle, Author, Description FROM books WHERE Genre = :genre ORDER BY RAND() LIMIT 1");
    $stmt->execute(['genre' => $genre]);
    $book = $stmt->fetch();

    if ($book) {
        echo "<h2>Random Book:</h2>";
        echo "<p><strong>Genre:</strong> " . htmlspecialchars($book['Genre']) . "</p>";
        echo "<p><strong>Title:</strong> " . htmlspecialchars($book['BookTitle']) . "</p>";
        echo "<p><strong>Author:</strong> " . htmlspecialchars($book['Author']) . "</p>";
        echo "<p><strong>Description:</strong> " . htmlspecialchars($book['Description']) . "</p>";
    } else {
        echo "<p>No books found in this genre.</p>";
    }
}
?>
<p><a href="index.php">Back to Home</a></p>
</body>
</html>
