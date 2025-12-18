<?php
/**
 * Upload Diagnostic Tool
 * Place this file in the root of your complaint_system folder
 * Access it via: http://yourschool.edu/complaint_system/check_upload.php
 */

echo "<h1>Upload Configuration Checker</h1>";
echo "<hr>";

// 1. Check PHP upload settings
echo "<h2>1. PHP Upload Settings</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";

$upload_max = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');
$max_execution = ini_get('max_execution_time');
$max_input = ini_get('max_input_time');

echo "<tr><td>upload_max_filesize</td><td>$upload_max</td><td>" . (parseSize($upload_max) >= 5242880 ? "✅ OK" : "❌ TOO LOW (need 5M)") . "</td></tr>";
echo "<tr><td>post_max_size</td><td>$post_max</td><td>" . (parseSize($post_max) >= 5242880 ? "✅ OK" : "❌ TOO LOW (need 5M)") . "</td></tr>";
echo "<tr><td>max_execution_time</td><td>$max_execution</td><td>✅</td></tr>";
echo "<tr><td>max_input_time</td><td>$max_input</td><td>✅</td></tr>";
echo "</table>";

// 2. Check uploads directory
echo "<h2>2. Uploads Directory Check</h2>";
$uploads_dir = __DIR__ . '/uploads';

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Check</th><th>Status</th><th>Details</th></tr>";

// Check if directory exists
$dir_exists = is_dir($uploads_dir);
echo "<tr><td>Directory Exists</td><td>" . ($dir_exists ? "✅ YES" : "❌ NO") . "</td><td>$uploads_dir</td></tr>";

if ($dir_exists) {
    // Check if writable
    $is_writable = is_writable($uploads_dir);
    echo "<tr><td>Directory Writable</td><td>" . ($is_writable ? "✅ YES" : "❌ NO") . "</td><td>" . ($is_writable ? "Can upload files" : "<strong>PERMISSION ISSUE!</strong>") . "</td></tr>";
    
    // Check permissions
    $perms = substr(sprintf('%o', fileperms($uploads_dir)), -4);
    echo "<tr><td>Permissions</td><td>" . ($perms == '0755' || $perms == '0777' ? "✅" : "⚠️") . "</td><td>$perms (should be 755 or 777)</td></tr>";
    
    // Check owner
    $owner = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($uploads_dir))['name'] : 'unknown';
    echo "<tr><td>Owner</td><td>ℹ️</td><td>$owner</td></tr>";
}

echo "</table>";

// 3. Test file creation
echo "<h2>3. Write Test</h2>";
$test_file = $uploads_dir . '/test_' . time() . '.txt';
$write_test = @file_put_contents($test_file, 'Test content');

if ($write_test !== false) {
    echo "✅ <strong>SUCCESS!</strong> Can write to uploads folder.<br>";
    echo "Test file created: " . basename($test_file) . "<br>";
    // Clean up
    @unlink($test_file);
    echo "Test file deleted.<br>";
} else {
    echo "❌ <strong>FAILED!</strong> Cannot write to uploads folder.<br>";
    echo "<strong style='color: red;'>THIS IS YOUR PROBLEM!</strong><br>";
    echo "<br><strong>Solution:</strong> Run this command on your server:<br>";
    echo "<code style='background: #f0f0f0; padding: 10px; display: block;'>chmod 755 uploads/</code>";
    echo "OR<br>";
    echo "<code style='background: #f0f0f0; padding: 10px; display: block;'>chmod 777 uploads/</code>";
}

// 4. Check tmp directory
echo "<h2>4. Temporary Upload Directory</h2>";
$tmp_dir = ini_get('upload_tmp_dir');
if (empty($tmp_dir)) {
    $tmp_dir = sys_get_temp_dir();
}
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>upload_tmp_dir</td><td>" . ($tmp_dir ? $tmp_dir : "<em>System Default</em>") . "</td></tr>";
echo "<tr><td>Writable</td><td>" . (is_writable($tmp_dir) ? "✅ YES" : "❌ NO") . "</td></tr>";
echo "</table>";

// 5. Test upload simulation
echo "<h2>5. Upload Test Form</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_upload'])) {
    echo "<h3>Upload Result:</h3>";
    echo "<pre>";
    print_r($_FILES['test_upload']);
    echo "</pre>";
    
    if ($_FILES['test_upload']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['test_upload']['tmp_name'];
        $name = basename($_FILES['test_upload']['name']);
        $upload_path = $uploads_dir . '/' . $name;
        
        if (move_uploaded_file($tmp_name, $upload_path)) {
            echo "<strong style='color: green;'>✅ UPLOAD SUCCESS!</strong><br>";
            echo "File saved to: $upload_path<br>";
            echo "File size: " . filesize($upload_path) . " bytes<br>";
            // Clean up
            @unlink($upload_path);
        } else {
            echo "<strong style='color: red;'>❌ UPLOAD FAILED!</strong><br>";
            echo "Could not move file from temp to uploads folder.<br>";
            echo "Check folder permissions!<br>";
        }
    } else {
        echo "<strong style='color: red;'>❌ UPLOAD ERROR!</strong><br>";
        echo "Error code: " . $_FILES['test_upload']['error'] . "<br>";
        echo getUploadErrorMessage($_FILES['test_upload']['error']);
    }
} else {
    echo '<form method="POST" enctype="multipart/form-data">';
    echo '<input type="file" name="test_upload" required><br><br>';
    echo '<button type="submit">Test Upload</button>';
    echo '</form>';
}

// Helper function
function parseSize($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    }
    return round($size);

    
}

function getUploadErrorMessage($error) {
    $messages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE in HTML form',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary upload folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk (permission issue)',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload',
    ];
    return $messages[$error] ?? 'Unknown error';
}

echo "<hr>";
echo "<h2>Quick Fixes</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;'>";
echo "<h3>If uploads are failing, try these commands on your server:</h3>";
echo "<ol>";
echo "<li><strong>Fix permissions (755):</strong><br><code>chmod 755 uploads/</code></li>";
echo "<li><strong>Fix permissions (777 - more permissive):</strong><br><code>chmod 777 uploads/</code></li>";
echo "<li><strong>Fix ownership (if you have sudo):</strong><br><code>chown -R www-data:www-data uploads/</code></li>";
echo "<li><strong>Or change owner to your user:</strong><br><code>chown -R yourusername:yourusername uploads/</code></li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Note:</strong> After fixing permissions, delete this file for security.</p>";
?>
