<?php

require "settings.inc.php";

$PDO = new PDO("mysql:host=".DB_SERVER."; dbname=".DB_NAME, DB_USER, DB_PASS);

$stmt = $PDO->prepare("INSERT INTO users (id_user, username, password, email, role_id_role) VALUES (:id, :username, :password, :email, :id_role)");

$stmt->bindValue(":id", 1);
$stmt->bindValue(":username", "root");
$stmt->bindValue(":password", password_hash("root", PASSWORD_BCRYPT));
$stmt->bindValue(":email", "root@example.com");
$stmt->bindValue(":id_role", 1);

$stmt->execute();

