<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classic Literature Explorer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f0f2f5;
            color: #2c3e50;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .hero-section {
            background: linear-gradient(135deg, #2c3e50 0%, #4ca1af 100%); 
            color: white;
            padding: 80px 0 100px;
            margin-bottom: -60px; 
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
        }

        .hero-title {
            font-family: 'Georgia', serif; 
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .control-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            background: white;
            padding: 30px;
            height: 100%;
            transition: transform 0.3s ease;
        }

        .control-card:hover {
            transform: translateY(-5px);
        }

        .control-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .icon-genre {
            background-color: #e3f2fd;
            color: #0d6efd;
        }

        .icon-author {
            background-color: #f3e5f5;
            color: #9c27b0;
        }

        .book-card {
            border: none;
            border-radius: 12px;
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .book-card:hover {
            box-shadow: 0 12px 20px rgba(0,0,0,0.1);
            transform: translateY(-3px);
        }

        .card-body {
            padding: 1.5rem;
            flex-grow: 1;
        }

        .book-title {
            font-family: 'Georgia', serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .book-author {
            color: #718096;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .book-desc {
            color: #4a5568;
            font-size: 0.95rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .genre-badge {
            background-color: #e2e8f0;
            color: #4a5568;
            font-size: 0.75rem;
            padding: 0.35em 0.8em;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            float: right;
            margin-bottom: 10px;
        }

        .section-header {
            border-left: 5px solid #4ca1af;
            padding-left: 15px;
            margin-bottom: 30px;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<?php
$host = '195.179.239.102';
$port = '3306';
$username = 'u198084402_test';
$password = 'TestPass25!'; 
$database_name = 'u198084402_DATA'; 
$table_name = 'books'; 

$dsn = "mysql:host=$host;port=$port;dbname=$database_name;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$books = [];
$error_msg = "";
$mode = $_GET['mode'] ?? 'home'; 
$selected_genre = $_GET['genre'] ?? '';
$selected_author = $_GET['author'] ?? '';
$genres_list = [];
$authors_list = [];
$page_title = "Featured Classics";
$page_subtitle = "A selection of timeless literature";

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    $stmt_g = $pdo->query("SELECT DISTINCT Genre FROM $table_name WHERE Genre IS NOT NULL AND Genre != '' ORDER BY Genre ASC");
    $genres_list = $stmt_g->fetchAll(PDO::FETCH_COLUMN);

    $stmt_a = $pdo->query("SELECT DISTINCT Author FROM $table_name WHERE Author IS NOT NULL AND Author != '' ORDER BY Author ASC");
    $authors_list = $stmt_a->fetchAll(PDO::FETCH_COLUMN);

    if ($mode === 'genre' && !empty($selected_genre)) {
        $sql = "SELECT * FROM $table_name WHERE Genre = :g ORDER BY RAND() LIMIT 4";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':g' => $selected_genre]);
        $books = $stmt->fetchAll();
        
        $page_title = "Recommendations: " . htmlspecialchars($selected_genre);
        $page_subtitle = "Here are some randomized suggestions for you.";

    } elseif ($mode === 'author' && !empty($selected_author)) {
        $sql = "SELECT * FROM $table_name WHERE Author = :a ORDER BY BookTitle ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':a' => $selected_author]);
        $books = $stmt->fetchAll();

        $page_title = "Collection: " . htmlspecialchars($selected_author);
        $page_subtitle = "Browse the complete list of works available.";

    } else {
        $sql = "SELECT * FROM $table_name ORDER BY RAND() LIMIT 6";
        $stmt = $pdo->query($sql);
        $books = $stmt->fetchAll();
    }

} catch (\PDOException $e) {
    $error_msg = $e->getMessage();
}
?>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="hero-title display-4 mb-3">Classic Literature Explorer</h1>
        <p class="lead opacity-75 mb-0" style="max-width: 600px; margin: 0 auto;">
            Not sure where to start? Use our tools to discover your next favorite book based on your mood or favorite writer.
        </p>
    </div>
</div>

<div class="container" style="position: relative; z-index: 10;">
    <div class="row g-4 justify-content-center">
        
        <div class="col-md-5">
            <div class="control-card">
                <div class="control-icon icon-genre">
                    <i class="fas fa-dice"></i>
                </div>
                <h3>Explore by Genre</h3>
                <p class="text-muted small">Select a genre to receive <strong>randomized</strong> classic book recommendations.</p>
                
                <form action="" method="GET" class="mt-4">
                    <input type="hidden" name="mode" value="genre">
                    <div class="input-group">
                        <select name="genre" class="form-select form-select-lg" required>
                            <option value="" selected disabled>Select a Genre...</option>
                            <?php foreach($genres_list as $g): ?>
                                <option value="<?php echo htmlspecialchars($g); ?>" <?php echo $selected_genre === $g ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($g); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-primary" type="submit">Recommend</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-5">
            <div class="control-card">
                <div class="control-icon icon-author">
                    <i class="fas fa-feather-alt"></i>
                </div>
                <h3>Browse by Author</h3>
                <p class="text-muted small">Select an author to view their full list of classic works available.</p>
                
                <form action="" method="GET" class="mt-4">
                    <input type="hidden" name="mode" value="author">
                    <div class="input-group">
                        <select name="author" class="form-select form-select-lg" required>
                            <option value="" selected disabled>Select an Author...</option>
                            <?php foreach($authors_list as $a): ?>
                                <option value="<?php echo htmlspecialchars($a); ?>" <?php echo $selected_author === $a ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($a); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-outline-dark" type="submit">View List</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<div class="container main-container pb-5">
    
    <div class="section-header">
        <h2 class="mb-1"><?php echo $page_title; ?></h2>
        <p class="text-muted mb-0"><?php echo $page_subtitle; ?></p>
    </div>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger shadow-sm rounded-3">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error_msg); ?>
        </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if (count($books) > 0): ?>
            <?php foreach ($books as $book): ?>
                <div class="col">
                    <div class="card book-card">
                        <div class="card-body">
                            <?php if (!empty($book['Genre'])): ?>
                                <span class="genre-badge"><?php echo htmlspecialchars($book['Genre']); ?></span>
                            <?php endif; ?>
                            
                            <div style="clear:both;"></div>

                            <h3 class="book-title mt-2"><?php echo htmlspecialchars($book['BookTitle']); ?></h3>
                            
                            <span class="book-author">
                                <i class="fas fa-pen-fancy me-1 text-muted"></i> 
                                <?php echo htmlspecialchars($book['Author'] ?: 'Unknown Author'); ?>
                            </span>
                            
                            <hr class="opacity-10 my-3">
                            
                            <p class="book-desc">
                                <?php echo htmlspecialchars($book['Description'] ?: 'No description available for this title.'); ?>
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0 pb-4 ps-4">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Read More</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted opacity-50">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h3>No books found</h3>
                    <p>Try selecting a different option above.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($mode !== 'home'): ?>
        <div class="text-center mt-5">
            <a href="?" class="btn btn-link text-muted">Clear Filters & Return Home</a>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
