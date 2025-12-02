<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Catalog</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0 40px;
            margin-bottom: 40px;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .hero-title {
            font-weight: 700;
            letter-spacing: -1px;
        }

        /* Search Bar */
        .search-container {
            background: white;
            padding: 8px;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: -30px auto 40px; /* Overlap hero */
            display: flex;
            align-items: center;
        }
        
        .search-input {
            border: none;
            flex-grow: 1;
            padding: 10px 20px;
            font-size: 1.1rem;
            outline: none;
            border-radius: 50px;
        }

        .search-btn {
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
        }

        /* Book Card */
        .book-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
            height: 100%;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        .book-title {
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
        }

        .book-desc {
            color: #4a5568;
            font-size: 0.95rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 4; /* Limit to 4 lines */
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
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
        }

        /* Sort Controls */
        .sort-controls a {
            text-decoration: none;
            font-size: 0.9rem;
            margin-left: 15px;
            color: #718096;
            font-weight: 500;
            transition: color 0.2s;
        }
        .sort-controls a:hover, .sort-controls a.active {
            color: #764ba2;
        }

    </style>
</head>
<body>

<?php
// ** Configuration **
$host = '195.179.239.102';
$port = '3306';
$username = 'u198084402_test';
$password = 'TestPass25!'; 
$database_name = 'u198084402_DATA'; 
$table_name = 'books'; // UPDATED to specific table

$dsn = "mysql:host=$host;port=$port;dbname=$database_name;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Initialize variables
$books = [];
$error_msg = "";
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Sorting Logic
$sort_col = isset($_GET['sort']) ? $_GET['sort'] : 'BookTitle';
$sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';
// Allowed sort columns for security
$allowed_sorts = ['BookTitle', 'Author', 'Genre'];
if (!in_array($sort_col, $allowed_sorts)) $sort_col = 'BookTitle';

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    // Build Query
    $sql = "SELECT * FROM $table_name";
    $params = [];

    // Add Search Filter
    if ($search_query) {
        $sql .= " WHERE BookTitle LIKE :q OR Author LIKE :q OR Genre LIKE :q OR Description LIKE :q";
        $params[':q'] = "%$search_query%";
    }

    // Add Sorting
    $sql .= " ORDER BY $sort_col $sort_order";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll();

} catch (\PDOException $e) {
    $error_msg = $e->getMessage();
}

// Helper to toggle sort order in links
function getSortLink($col, $currentCol, $currentOrder, $search) {
    $newOrder = ($col === $currentCol && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
    $searchPart = $search ? "&q=" . urlencode($search) : "";
    return "?sort=$col&order=$newOrder" . $searchPart;
}
?>

<!-- Hero Header -->
<div class="hero-section text-center">
    <div class="container">
        <h1 class="hero-title display-4 mb-2"><i class="fas fa-book-open me-3"></i>Library Catalog</h1>
        <p class="lead opacity-75">Discover your next great read</p>
    </div>
</div>

<!-- Search Bar (Floating) -->
<div class="container">
    <form action="" method="GET">
        <div class="search-container">
            <i class="fas fa-search text-muted ps-3"></i>
            <input type="text" name="q" class="search-input" placeholder="Search by title, author, or genre..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="btn btn-primary search-btn">Search</button>
        </div>
        <!-- Preserve sort if searching -->
        <?php if(isset($_GET['sort'])): ?>
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort_col); ?>">
            <input type="hidden" name="order" value="<?php echo htmlspecialchars($sort_order); ?>">
        <?php endif; ?>
    </form>
</div>

<div class="container main-container pb-5">
    
    <!-- Sort Controls -->
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div class="text-muted">
            Found <strong><?php echo count($books); ?></strong> books
        </div>
        <div class="sort-controls">
            <span class="text-muted me-2">Sort by:</span>
            <a href="<?php echo getSortLink('BookTitle', $sort_col, $sort_order, $search_query); ?>" class="<?php echo $sort_col === 'BookTitle' ? 'active' : ''; ?>">
                Title <?php if($sort_col === 'BookTitle') echo $sort_order === 'ASC' ? '↑' : '↓'; ?>
            </a>
            <a href="<?php echo getSortLink('Author', $sort_col, $sort_order, $search_query); ?>" class="<?php echo $sort_col === 'Author' ? 'active' : ''; ?>">
                Author <?php if($sort_col === 'Author') echo $sort_order === 'ASC' ? '↑' : '↓'; ?>
            </a>
            <a href="<?php echo getSortLink('Genre', $sort_col, $sort_order, $search_query); ?>" class="<?php echo $sort_col === 'Genre' ? 'active' : ''; ?>">
                Genre <?php if($sort_col === 'Genre') echo $sort_order === 'ASC' ? '↑' : '↓'; ?>
            </a>
        </div>
    </div>

    <!-- Error Alert -->
    <?php if ($error_msg): ?>
        <div class="alert alert-danger shadow-sm rounded-3">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error_msg); ?>
        </div>
    <?php endif; ?>

    <!-- Content Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if (count($books) > 0): ?>
            <?php foreach ($books as $book): ?>
                <div class="col">
                    <div class="card book-card h-100 position-relative">
                        <div class="card-body">
                            <!-- Genre Badge -->
                            <?php if (!empty($book['Genre'])): ?>
                                <span class="genre-badge"><?php echo htmlspecialchars($book['Genre']); ?></span>
                            <?php endif; ?>
                            
                            <!-- Title -->
                            <h3 class="book-title mt-2"><?php echo htmlspecialchars($book['BookTitle']); ?></h3>
                            
                            <!-- Author -->
                            <span class="book-author">
                                <i class="fas fa-pen-nib me-1 small"></i> 
                                <?php echo htmlspecialchars($book['Author'] ?: 'Unknown Author'); ?>
                            </span>
                            
                            <hr class="opacity-10 my-3">
                            
                            <!-- Description -->
                            <p class="book-desc">
                                <?php echo htmlspecialchars($book['Description'] ?: 'No description available.'); ?>
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0 pb-4 ps-4">
                            <button class="btn btn-sm btn-outline-primary rounded-pill">View Details</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted opacity-50">
                    <i class="fas fa-book fa-4x mb-3"></i>
                    <h3>No books found</h3>
                    <p>Try adjusting your search terms.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
