<?php
// upload.php

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excelFile'])) {
    $file = $_FILES['excelFile'];
    $filename = basename($file['name']);
    $targetPath = $uploadDir . $filename;

    // Validate file type
    $allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    if (!in_array($file['type'], $allowedTypes)) {
        echo "Invalid file type. Please upload a .xlsx file.";
        exit;
    }

    // Move file to upload folder
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo "✅ File uploaded successfully to: <code>uploads/$filename</code>";
    } else {
        echo "❌ Failed to upload file.";
    }
} else {
    echo "⚠️ No file uploaded.";
}
?>
