<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Viewer</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f4f6f9; /* Calming light gray/blue background */
            color: #495057;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); /* Soft shadow */
            overflow: hidden; /* Keeps table corners rounded */
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #edf2f9;
            padding: 25px;
        }

        .card-header h2 {
            color: #2c3e50; /* Dark blue/gray for contrast */
            font-weight: 600;
            margin: 0;
            font-size: 1.75rem;
        }

        .table-responsive {
            padding: 0;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            border-bottom: 2px solid #e9ecef;
            padding: 15px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .table thead th:hover {
            background-color: #e9ecef;
            color: #495057;
        }

        .table thead th a {
            text-decoration: none;
            color: inherit;
            display: block;
            width: 100%;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f5;
            color: #555;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f5f9; /* Very subtle hover effect */
        }

        /* Status Badge Styles (Optional, works if you have a 'status' column) */
        .badge-soft-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .badge-soft-danger {
            background-color: #f8d7da;
            color: #842029;
        }

        .sort-icon {
            float: right;
            opacity: 0.3;
        }
        .sort-active {
            opacity: 1;
            color: #0d6efd;
        }
    </style>
</head>
<body>

<div class="container main-container">
    <div class="row justify-content-center">
        <div class="col-12">
            
            <?php
            // ** Configuration **
            $host = '195.179.239.102';
            $port = '3306';
            $username = 'u198084402_test';
            $password = 'TestPass25!'; 
            $database_name = 'u198084402_DATA'; 
            $table_name = 'MarksTable'; // Change this if you want to view 'books' or 'authors'

            // Data Source Name
            $dsn = "mysql:host=$host;port=$port;dbname=$database_name;charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                // 1. Connect
                $pdo = new PDO($dsn, $username, $password, $options);

                // 2. Get Column Names (to validate sorting and build headers)
                // We fetch one empty row just to get metadata efficiently
                $stmt_cols = $pdo->prepare("SELECT * FROM $table_name LIMIT 1");
                $stmt_cols->execute();
                $columns = [];
                for ($i = 0; $i < $stmt_cols->columnCount(); $i++) {
                    $meta = $stmt_cols->getColumnMeta($i);
                    $columns[] = $meta['name'];
                }

                // 3. Handle Sorting
                // Default sort
                $sort_column = isset($columns[0]) ? $columns[0] : '';
                $sort_order = 'ASC';

                // Check if user clicked a header
                if (isset($_GET['sort']) && in_array($_GET['sort'], $columns)) {
                    $sort_column = $_GET['sort'];
                }

                if (isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC'])) {
                    $sort_order = strtoupper($_GET['order']);
                }

                // Calculate next order for the link (if currently ASC, next click makes it DESC)
                $next_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC';

                // 4. Fetch Data with Sorting
                // Note: Column names can't be bound as parameters in PDO, so we whitelisted them above (in_array check)
                $sql = "SELECT * FROM $table_name ORDER BY $sort_column $sort_order";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                ?>

                <!-- UI: Card Container -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h2><?php echo htmlspecialchars($table_name); ?> Data</h2>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                Sorted by <strong><?php echo htmlspecialchars($sort_column); ?></strong> 
                                (<?php echo htmlspecialchars($sort_order); ?>)
                            </p>
                        </div>
                        <div>
                            <!-- Placeholder for actions like "Export" or "Add New" -->
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <?php foreach ($columns as $col): ?>
                                            <th>
                                                <a href="?sort=<?php echo urlencode($col); ?>&order=<?php echo ($col === $sort_column) ? $next_order : 'ASC'; ?>">
                                                    <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $col))); ?>
                                                    
                                                    <!-- Sort Icons -->
                                                    <?php if ($col === $sort_column): ?>
                                                        <i class="fas fa-sort-<?php echo ($sort_order === 'ASC') ? 'up' : 'down'; ?> sort-icon sort-active"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-sort sort-icon"></i>
                                                    <?php endif; ?>
                                                </a>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $rowCount = 0;
                                    while ($row = $stmt->fetch()): 
                                        $rowCount++;
                                    ?>
                                        <tr>
                                            <?php foreach ($row as $value): ?>
                                                <td><?php echo htmlspecialchars($value); ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endwhile; ?>

                                    <?php if ($rowCount === 0): ?>
                                        <tr>
                                            <td colspan="<?php echo count($columns); ?>" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                                                    <p>No records found in this table.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 py-3">
                        <small class="text-muted">Showing all <?php echo $rowCount; ?> rows</small>
                    </div>
                </div>

            <?php
            } catch (\PDOException $e) {
                ?>
                <div class="alert alert-danger shadow-sm rounded-3" role="alert">
                    <h4 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Database Connection Error</h4>
                    <p>We couldn't connect to the database. Please check your settings.</p>
                    <hr>
                    <p class="mb-0 font-monospace small"><?php echo htmlspecialchars($e->getMessage()); ?></p>
                </div>
                <?php
            }
            
            // Close connection
            $pdo = null;
            ?>

        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
