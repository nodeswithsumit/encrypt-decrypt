<?php
// require_once '../../config/config.php';
// require_once '../../functions/encryption.php';

$host = 'ibmice.czgm84kc6wqr.ap-south-1.rds.amazonaws.com';
$dbname = 'demo';
$username = 'iceadmin';
$password = 'TheAnalytix';

$conn = mysqli_connect($host, $username, $password, $dbname);

if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
$encryption_key = base64_encode(openssl_random_pseudo_bytes(32));
echo "Your secret encryption key: " . $encryption_key;
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
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $encrypted_password = encryptData($password, ENCRYPTION_KEY);

    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$encrypted_password')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Registration successful!";
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form method="post">
        <label>Username:</label><br>
        <input type="text" name="username" required><br>
        <label>Email:</label><br>
        <input type="email" name="email" required><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>
