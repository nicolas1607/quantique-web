<?php

$id = $_POST['id'];

try {
    $conn = new PDO('mysql:host=127.0.0.1:3306;dbname=oeyl7548_office', 'oeyl7548_office', 'Nico2021-');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

$stmt = $conn->prepare("DELETE FROM note WHERE id = " . $id);
$stmt->execute();
