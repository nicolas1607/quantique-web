<?php

$user = $_POST['user'];
$contract = $_POST['contract'];
$msg = $_POST['msg'];
$datetime = new DateTime();
$date = $datetime->format('Y-m-d H:i:s');

header('Content-Type: application/json');

try {
    $conn = new PDO('mysql:host=127.0.0.1:8889;dbname=quantique-web', 'root', 'root');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

$stmt = $conn->prepare(
    "INSERT INTO note (user_id, contract_id, message, released_at) 
                    VALUES (:user, :contract, :msg, :date)"
);
$stmt->bindParam(':user', $user);
$stmt->bindParam(':contract', $contract);
$stmt->bindParam(':msg', $msg);
$stmt->bindParam(':date', $date);
$stmt->execute();
