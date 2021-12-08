<?php

$id = $_POST['id'];

try {
    $conn = new PDO('mysql:host=127.0.0.1:8889;dbname=quantique-web', 'root', 'root');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

$stmt = $conn->prepare("DELETE FROM note WHERE id = " . $id);
$stmt->execute();
