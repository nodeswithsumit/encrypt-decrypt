<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'encryption_demo');

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
$encryption_key = base64_encode(openssl_random_pseudo_bytes(32));
echo "Your secret encryption key: " . $encryption_key;
define('ENCRYPTION_KEY', '$encryption_key'); // Replace with your actual key
?>

