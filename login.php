<?php

# go to the url : https://shorturl.at/WtckM to see code. 

$host = 'ibmice.czgm84kc6wqr.ap-south-1.rds.amazonaws.com';
$dbname = 'demo';
$username = 'iceadmin';
$password = 'TheAnalytix';

$conn = mysqli_connect($host, $username, $password, $dbname);

if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

#generation fir encrypted key
$encryption_key = base64_encode(openssl_random_pseudo_bytes(32));
# show to user
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
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $decrypted_password = decryptData($row['password'], ENCRYPTION_KEY);

        if ($password == $decrypted_password) {
            echo "Login successful! Welcome, " . $row['username'];
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with this email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="post">
        <label>Email:</label><br>
        <input type="email" name="email" required><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
