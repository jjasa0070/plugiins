<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP File Scanner</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>PHP File Scanner</h1>
    <?php
    // Direktori yang ingin discan (sesuaikan jika perlu)
    $directory = __DIR__;
    $extensions = ['php', 'php5', 'phtml'];
    $results = [];

    // Fungsi untuk scan file dalam direktori
    function scanDirectory($dir)
    {
        global $extensions, $results;
        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $path = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($path)) {
                scanDirectory($path);
            } else {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if (in_array($ext, $extensions)) {
                    scanFile($path);
                }
            }
        }
    }

    // Fungsi untuk memindai file
    function scanFile($file)
    {
        global $results;
        $content = file_get_contents($file);
        $moveUploadedFile = strpos($content, 'move_uploaded_file') !== false;
        $isEncrypted = preg_match('/(eval\(|base64_decode\(|gzinflate\(|str_rot13\(|gzuncompress\(|pack\()/i', $content);

        if ($moveUploadedFile || $isEncrypted) {
            $results[] = [
                'file' => $file,
                'move_uploaded_file' => $moveUploadedFile,
                'encrypted_code' => $isEncrypted,
            ];
        }
    }

    // Aksi hapus file jika tombol hapus ditekan
    if (isset($_GET['delete'])) {
        $fileToDelete = $_GET['delete'];
        if (file_exists($fileToDelete)) {
            unlink($fileToDelete);
            echo "<p>File " . htmlspecialchars($fileToDelete) . " telah dihapus.</p>";
        }
    }

    // Menjalankan scan
    scanDirectory($directory);

    // Tampilkan hasil scan dalam tabel
    if (!empty($results)) {
        echo "<h2>Hasil Scan:</h2>";
        echo "<table>
                <tr>
                    <th>File</th>
                    <th>move_uploaded_file</th>
                    <th>Encrypted Code</th>
                    <th>Aksi</th>
                </tr>";
        foreach ($results as $result) {
            $file = htmlspecialchars($result['file']);
            $moveUploadedFile = $result['move_uploaded_file'] ? 'Ya' : 'Tidak';
            $isEncrypted = $result['encrypted_code'] ? 'Ya' : 'Tidak';
            echo "<tr>
                    <td>$file</td>
                    <td>$moveUploadedFile</td>
                    <td>$isEncrypted</td>
                    <td>
                        <a href='" . $file . "' target='_blank'>Lihat</a> | 
                        <a href='?delete=" . urlencode($file) . "' onclick=\"return confirm('Apakah Anda yakin ingin menghapus file ini?');\">Hapus</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p><strong>Tidak ada file mencurigakan yang ditemukan.</strong></p>";
    }
    ?>
</body>
</html>
