<?php
// Set the directory
$directory = realpath('/home/judas/Documents/PHP Scripts');

// Check if directory exists
if (!$directory || !is_dir($directory)) {
    die("Error: Directory not found or is not readable.");
}

// Get all PHP files manually using scandir()
$files = scandir($directory);

// Filter out only .php files
$phpFiles = array_filter($files, function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'php';
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP File Index</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('blockwavemoon.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #F5F5F5; /* Light text color for contrast */
            padding: 20px;
        }

        /* Directory card styling */
        .directory-card {
            background-color: #191724; /* Dark purple background */
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 40px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            text-align: center;
            word-wrap: break-word; /* Ensure long text wraps within the card */
            overflow-wrap: break-word; /* Same as word-wrap, for better compatibility */
            white-space: normal; /* Allows long text to wrap within the container */
        }

        .directory-card h2 {
            color: #F6C177; /* Primary accent color (golden yellow) for text */
            font-size: 2.8em;
            font-weight: 600;
            word-wrap: break-word; /* Allow long text in heading to wrap */
            overflow-wrap: break-word; /* Same as above */
        }

        /* Styling the file list container */
        .container {
            width: 80%;
            margin: 0 auto;
            max-width: 1200px;
        }

        /* Card layout for files in grid */
        .file-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 columns */
            gap: 20px; /* Spacing between grid items */
            text-align: center;
        }

        .file-card {
            background-color: #191724; /* Dark purple for cards */
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease-in-out;
        }

        .file-card:hover {
            transform: translateY(-5px); /* Subtle lift effect on hover */
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
        }

        .file-card a {
            text-decoration: none;
            color: #F5F5F5; /* Light color for links */
            font-weight: bold;
            font-size: 1.4em;
            padding: 8px 20px;
            border-radius: 5px;
            display: inline-block;
            transition: all 0.3s ease-in-out;
        }

        .file-card a:hover {
            color: #26233A; /* Dark color on hover */
            background-color: #F6C177; /* Primary yellow background on hover */
            transform: scale(1.1); /* Subtle scaling effect */
        }

        /* Mobile Responsiveness */
        @media screen and (max-width: 768px) {
            h2 {
                font-size: 2.2em;
            }

            .container {
                width: 90%;
            }

            .file-grid {
                grid-template-columns: 1fr 1fr; /* 2 columns on smaller screens */
            }

            .file-card {
                padding: 15px;
            }

            .file-card a {
                font-size: 1.2em;
                padding: 6px 18px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Directory Card -->
        <div class="directory-card">
            <h2>PHP Files in <?php echo htmlspecialchars($directory); ?></h2>
        </div>

        <!-- Grid for PHP Files -->
        <div class="file-grid">
            <?php
            if (!empty($phpFiles)) {
                foreach ($phpFiles as $file) {
                    echo "<div class='file-card'><a href='" . htmlspecialchars($file) . "'>$file</a></div>";
                }
            } else {
                echo "<p>No PHP files found.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
