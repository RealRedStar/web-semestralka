<?php

namespace redstar\Models;


use PDO;

/**
 * Třída pro práci s databází
 * @package redstar\Models
 */
class DatabaseModel
{
    /** @var DatabaseModel $database - Instance databázového modelu. */
    private static $database;

    /** @var \PDO $pdo Instance pracující s databází prostřednictvím PDO. */
    private $pdo;

    /**
     * Konstruktor pro tvorbu instance
     */
    private function __construct() {
        $this->pdo = new \PDO("mysql:host=".DB_SERVER."; dbname=".DB_NAME, DB_USER, DB_PASS);

    }
    /**
     * Tovární metoda pro poskytnutí singletonu databázového modelu.
     * @return DatabaseModel Databázový model
     */
    public static function getDatabaseModel() {
        if (empty(self::$database)){
            self::$database = new DatabaseModel();
        }
        return self::$database;
    }

    public function getUserCredentials(string $username) {
        $stmt = $this->pdo->prepare("SELECT username, password FROM users WHERE username = :username");

        $stmt->bindValue(":username", $username);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getUserDataById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id_user = :id");

        $stmt->bindValue(":id", $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getUserDataByUsername(string $username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");

        $stmt->bindValue(":username", $username);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getUserDataByEmail(string $email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");

        $stmt->bindValue(":email", $email);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function addUserToDatabase($data) {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, email, first_name, last_name, image_name, role_id_role) VALUES (:username, :password, :email, :firstName, :lastName, :imageName, :role)");

        $stmt->bindValue(":username", $data["username"]);
        $stmt->bindValue(":password", $data["password"]);
        $stmt->bindValue(":email", $data["email"]);
        $stmt->bindValue(":firstName", $data["first-name"]);
        $stmt->bindValue(":lastName", $data["last-name"]);
        $stmt->bindValue(":imageName", $data["image-name"]);
        $stmt->bindValue(":role", $data["role"]);
        return $stmt->execute();
    }

    public function getAllUsernamesFromDatabase() {
        $stmt = $this->pdo->prepare("SELECT username FROM users");

        $stmt->execute();

        $data = array();

        for ($i = 0; $row = $stmt->fetch(PDO::FETCH_NUM); $i++) {
            $data[$i] = $row[0];
        }

        return $data;
    }

    public function getAllEmailsFromDatabase() {
        $stmt = $this->pdo->prepare("SELECT email FROM users");

        $stmt->execute();

        $data = array();

        for ($i = 0; $row = $stmt->fetch(PDO::FETCH_NUM); $i++) {
            $data[$i] = $row[0];
        }

        return $data;
    }

    public function getMatchByIdFromDatabase(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM matches WHERE id_match = :id_match");

        $stmt->bindValue(":id_match", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllMatchesFromDatabase() {
        $stmt = $this->pdo->prepare("SELECT * FROM matches");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMatchesCount() {
        $stmt = $this->pdo->prepare("SELECT COUNT(id_match) FROM matches");
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    public function getMatchPlayerIdsFromDatabase(int $matchId): array {
        $stmt = $this->pdo->prepare("SELECT id_user FROM players_list WHERE id_match = :id_match");

        $stmt->bindValue(":id_match", $matchId);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMatchBannedPlayerIdsFromDatabase(int $matchId): array {
        $stmt = $this->pdo->prepare("SELECT id_user FROM banned_players_from_matches WHERE id_match = :id_match");

        $stmt->bindValue(":id_match", $matchId);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRoleByIdFromDatabase(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE id_role = :id_role");

        $stmt->bindValue(":id_role", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}