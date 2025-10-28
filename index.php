<?php
// index.php

// Define the content for clarity, although a simple echo would also work
$content = "TEST2";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple PHP Test Page</title>
    <style>
        /* CSS for a simple, good-looking, and centered display */
        body {
            /* Full viewport height to allow centering */
            height: 100vh;
            /* Remove default margin */
            margin: 0;
            /* Basic font styling */
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9; /* Light, subtle background */

            /* Flexbox for perfect vertical and horizontal centering */
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
        }

        .container {
            /* Styles for the "TEST" word itself */
            font-size: 5em; /* Large text */
            font-weight: bold;
            color: #333; /* Dark text color */
            padding: 20px 40px;
            border: 5px solid #007bff; /* Primary color border */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
            text-transform: uppercase;
            letter-spacing: 5px;
            background-color: #ffffff; /* White background for the box */
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // PHP output: The content variable contains "TEST"
        echo $content;
        ?>
    </div>
</body>
</html>
