<?php

// **1. Database Connection Details**
$host = '195.179.239.102';
$port = '3306';
$username = 'u198084402_test';
$password = 'TestPass25!'; // <--- REPLACE with your actual password!
$database_name = 'u198084402_DATA'; // <--- REPLACE with the specific database name!

// The table you want to read (assuming you know a table name)
$table_name = 'MarksTable'; // <--- REPLACE with a real table name from your database!

// **2. Construct the Data Source Name (DSN)**
$dsn = "mysql:host=$host;port=$port;dbname=$database_name;charset=utf8mb4";

// **3. Set PDO Options**
$options = [
    // Throw an exception for any database errors (recommended for development)
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Set default fetch mode to associative array
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Disable emulation mode for prepared statements
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     // **4. Create PDO Connection**
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "Connected to the database successfully!<br>";

    // **5. Prepare and Execute the Query**
    // Using a prepared statement for a simple SELECT is a good habit.
    $stmt = $pdo->prepare("SELECT * FROM $table_name");
    $stmt->execute();
    
    // **6. Fetch and Display Results**
    echo "<h2>Contents of table: " . htmlspecialchars($table_name) . "</h2>";
    echo "<table border='1'><thead><tr>";
    
    // Get column names for table header
    for ($i = 0; $i < $stmt->columnCount(); $i++) {
        $meta = $stmt->getColumnMeta($i);
        echo "<th>" . htmlspecialchars($meta['name']) . "</th>";
    }
    echo "</tr></thead><tbody>";

    // Loop through all rows
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</tbody></table>";

} catch (\PDOException $e) {
    // **7. Handle Connection/Query Errors**
    echo "Connection failed: " . $e->getMessage();
    // In a production environment, avoid echoing the full error message for security.
}

// **8. Close Connection (optional, but good practice)**
$pdo = null;

?>
