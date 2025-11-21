<?php
include 'index.php';

// Fetch authors and genres
$authors = $pdo->query("SELECT Author FROM authors")->fetchAll(PDO::FETCH_COLUMN);
$genres = $pdo->query("SELECT Genre FROM genres")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Books</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Browse Books</h1>

<form method="post">
    <label for="author">Choose an author:</label>
    <select name="author" id="author">
        <option value="">--Select Author--</option>
        <?php foreach($authors as $author): ?>
            <option value="<?php echo htmlspecialchars($author); ?>"><?php echo htmlspecialchars($author); ?></option>
        <?php endforeach; ?>
    </select>

    <label for="genre">Or choose a genre:</label>
    <select name="genre" id="genre">
        <option value="">--Select Genre--</option>
        <?php foreach($genres as $genre): ?>
            <option value="<?php echo htmlspecialchars($genre); ?>"><?php echo htmlspecialchars($genre); ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Browse</button>
</form>

<?php
if (isset($_POST['author']) && $_POST['author'] !== "") {
    $stmt = $pdo->prepare("SELECT Genre, BookTitle, Author, Description FROM books WHERE Author = :author");
    $stmt->execute(['author' => $_POST['author']]);
} elseif (isset($_POST['genre']) && $_POST['genre'] !== "") {
    $stmt = $pdo->prepare("SELECT Genre, BookTitle, Author, Description FROM books WHERE Genre = :genre");
    $stmt->execute(['genre' => $_POST['genre']]);
} else {
    $stmt = null;
}

if ($stmt) {
    $books = $stmt->fetchAll();
    if ($books) {
        echo "<h2>Books Found:</h2>";
        foreach ($books as $book) {
            echo "<p><strong>Genre:</strong> " . htmlspecialchars($book['Genre']) . "<br>";
            echo "<strong>Title:</strong> " . htmlspecialchars($book['BookTitle']) . "<br>";
            echo "<strong>Author:</strong> " . htmlspecialchars($book['Author']) . "<br>";
            echo "<strong>Description:</strong> " . htmlspecialchars($book['Description']) . "</p><hr>";
        }
    } else {
        echo "<p>No books found.</p>";
    }
}
?>
<p><a href="index.php">Back to Home</a></p>
</body>
</html>
