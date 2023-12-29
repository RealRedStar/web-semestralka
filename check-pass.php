<?php

require "settings.inc.php";

$PDO = new PDO("mysql:host=".DB_SERVER."; dbname=".DB_NAME, DB_USER, DB_PASS);

$stmt = $PDO->prepare("SELECT password FROM users WHERE username = :username");

$stmt->bindValue(":username", "root");
$stmt->execute();
$result = $stmt->fetch();

if (password_verify("root", $result[0])) {
    echo "SUCCESS!";
} else {
    echo "NO!";
}