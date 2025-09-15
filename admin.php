<?php
// admin.php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// CONFIGURATION
$downloadUrl = 'https://www.delltechnologies.com/asset/en-us/products/converged-infrastructure/technical-support/dell-ax-solutions-for-microsoft-hardware-configurations-guide.xlsx';
$downloadFolder = __DIR__ . "/downloads";
$uploadFolder = __DIR__ . "/uploads";
$filename = "dell-ax-solutions-for-microsoft-hardware-configurations-guide.xlsx";
$downloadPath = "$downloadFolder/$filename";
$uploadPath = "$uploadFolder/$filename";

// Ensure folders exist
if (!is_dir($downloadFolder)) mkdir($downloadFolder, 0777, true);
if (!is_dir($uploadFolder)) mkdir($uploadFolder, 0777, true);

// 1. Download the file
if (isset($_GET['action']) && $_GET['action'] === 'download') {
    file_put_contents($downloadPath, file_get_contents($downloadUrl));
    echo "File downloaded to $downloadPath";
}

// 2. Move file to upload folder
if (isset($_GET['action']) && $_GET['action'] === 'move') {
    if (file_exists($downloadPath)) {
        rename($downloadPath, $uploadPath);
        echo "File moved to $uploadPath";
    } else {
        echo "Download file not found.";
    }
}

// 3. Parse Excel and insert into MySQL
if (isset($_GET['action']) && $_GET['action'] === 'parse') {
    if (!file_exists($uploadPath)) {
        die("Excel file not found in upload folder.");
    }

    $spreadsheet = IOFactory::load($uploadPath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    // Connect to MySQL
    $mysqli = new mysqli("localhost", "your_db_user", "your_db_pass", "your_db_name");
    if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

    foreach ($rows as $index => $row) {
        if ($index === 0) continue; // Skip header row
        $col1 = $mysqli->real_escape_string($row[0]);
        $col2 = $mysqli->real_escape_string($row[1]);
        // Add more columns as needed
        $sql = "INSERT INTO your_table (column1, column2) VALUES ('$col1', '$col2')";
        $mysqli->query($sql);
    }

    echo "Excel data imported to database.";
}
?>
