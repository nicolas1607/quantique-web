<?php

$username = 'root';
$password = 'root';

try {
    $conn = new PDO('mysql:host=127.0.0.1:8889;dbname=quantique-web', $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $msg = str_replace("\r\n", "\n", $request->get('message'));
    $datetime = new DateTime();
    $date = $datetime->format('Y-m-d H:i:s');
    $user = $this->getUser()->getId();
    $contract = $contract->getId();

    $stmt = $conn->prepare(
        "INSERT INTO note (user_id, contract_id, message, released_at) 
                VALUES (:user, :contract, :msg, :date)"
    );
    $stmt->bindParam(':user', $user);
    $stmt->bindParam(':contract', $contract);
    $stmt->bindParam(':msg', $msg);
    $stmt->bindParam(':date', $date);
    $stmt->execute();
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
