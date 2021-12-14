<?php

$user = $_POST['user'];
$contract = $_POST['contract'];
$msg = $_POST['msg'];
$msg = str_replace("\r\n", "\n", $_POST['msg']);
$datetime = new DateTime();
$date = $datetime->format('Y-m-d H:i:s');

try {
    $conn = new PDO('mysql:host=127.0.0.1:8889;dbname=quantique-web', 'root', 'root');
    // $conn = new PDO('mysql:host=127.0.0.1:3306;dbname=oeyl7548_office', 'oeyl7548_office', 'Nico2021-');
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
