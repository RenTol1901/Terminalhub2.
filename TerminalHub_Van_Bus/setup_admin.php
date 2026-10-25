<?php
require_once __DIR__ . '/config/database.php';

$email = 'admin@terminalhub.local';
$password = password_hash('Admin123!', PASSWORD_DEFAULT);
$name = 'TerminalHub Admin';

$stmt = $conn->prepare(
    "SELECT id FROM users WHERE email = ?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Admin account already exists.";
} else {
    $role = 'admin';
    $stmt->close();

    $stmt = $conn->prepare(
        "INSERT INTO users (name, email, password, role)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        echo "Admin account created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>