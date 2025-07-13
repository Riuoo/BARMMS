<?php
// Simple test to check image upload functionality
echo "<h1>Image Upload Test</h1>";

// Check if uploads directory exists and is writable
$uploadDir = 'public/uploads/projects';
echo "<h2>Directory Check:</h2>";
echo "Uploads directory exists: " . (file_exists($uploadDir) ? 'Yes' : 'No') . "<br>";
echo "Uploads directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "<br>";

// Check if we can create a test file
$testFile = $uploadDir . '/test.txt';
if (file_put_contents($testFile, 'test')) {
    echo "Can create files in uploads directory: Yes<br>";
    unlink($testFile); // Clean up
} else {
    echo "Can create files in uploads directory: No<br>";
}

// Check form enctype
echo "<h2>Form Test:</h2>";
echo '<form action="" method="POST" enctype="multipart/form-data">';
echo '<input type="file" name="test_image" accept="image/*"><br>';
echo '<input type="submit" value="Test Upload">';
echo '</form>';

if ($_POST && isset($_FILES['test_image'])) {
    echo "<h2>Upload Test Results:</h2>";
    $file = $_FILES['test_image'];
    echo "File uploaded: " . ($file['error'] === UPLOAD_ERR_OK ? 'Yes' : 'No') . "<br>";
    echo "File name: " . $file['name'] . "<br>";
    echo "File size: " . $file['size'] . " bytes<br>";
    echo "File type: " . $file['type'] . "<br>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $uploadPath = $uploadDir . '/' . time() . '_' . $file['name'];
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            echo "File moved successfully to: " . $uploadPath . "<br>";
            echo "<img src='" . $uploadPath . "' style='max-width: 200px;'><br>";
        } else {
            echo "Failed to move uploaded file<br>";
        }
    }
}
?> 