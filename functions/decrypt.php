<?php
$host = 'ibmice.czgm84kc6wqr.ap-south-1.rds.amazonaws.com';
$dbname = 'demo';
$username = 'iceadmin';
$password = 'TheAnalytix';

$conn = mysqli_connect($host, $username, $password, $dbname);

if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
$encryption_key = base64_encode(openssl_random_pseudo_bytes(32));
// echo "Your secret encryption key: " . $encryption_key;
define('ENCRYPTION_KEY', '$encryption_key'); // Replace with your actual key

# encryption.php
function encryptData($data, $key) {
    $encryption_key = base64_decode($key);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

function decryptData($data, $key) {
    $encryption_key = base64_decode($key);
    list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the encrypted data from the form input
    $encrypted_data = $_POST['encrypted_data'];

    // Decrypt the data
    $decrypted_data = decryptData($encrypted_data, ENCRYPTION_KEY);

    echo "Decrypted data: " . htmlspecialchars($decrypted_data);
}
?>
