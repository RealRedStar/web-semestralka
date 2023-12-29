<?php

require "settings.inc.php";

$username = "root";

$PDO = new PDO("mysql:host=".DB_SERVER."; dbname=".DB_NAME, DB_USER, DB_PASS);

$stmt = $PDO->prepare("SELECT username, password FROM users WHERE username = :username");

$stmt->bindValue(":username", $username);
$stmt->execute();

$res = $stmt->fetch();

var_dump($res);

