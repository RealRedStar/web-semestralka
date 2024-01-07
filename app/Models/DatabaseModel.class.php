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

        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    public function addPlayerToMatch(int $playerId, int $matchId): bool {
        $stmt = $this->pdo->prepare("INSERT INTO players_list (id_match, id_user) VALUES (:id_match, :id_player)");

        $stmt->bindValue(":id_match", $matchId);
        $stmt->bindValue(":id_player", $playerId);

        return $stmt->execute();
    }

    public function removePlayerFromMatch(int $playerId, int $matchId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM players_list WHERE (id_match = :id_match and id_user = :id_player)");

        $stmt->bindValue(":id_match", $matchId);
        $stmt->bindValue(":id_player", $playerId);

        return $stmt->execute();
    }

    public function banPlayerFromMatch(int $playerId, int $matchId): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO banned_players_from_matches (id_user, id_match) VALUES (:id_player, :id_match)");

        $stmt->bindValue(":id_match", $matchId);
        $stmt->bindValue(":id_player", $playerId);

        return $stmt->execute();
    }

    public function unbanPlayerFromMatch(int $playerId, int $matchId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM banned_players_from_matches WHERE (id_match = :id_match and id_user = :id_player)");

        $stmt->bindValue(":id_match", $matchId);
        $stmt->bindValue(":id_player", $playerId);

        return $stmt->execute();
    }

    public function getNationByTag(string $tag) {
        $stmt = $this->pdo->prepare("SELECT * FROM nations WHERE tag = :tag");

        $stmt->bindValue(":tag", $tag);
        $stmt->execute();

        return $stmt->fetch();
    }


    public function getNationByName(string $name) {
        $stmt = $this->pdo->prepare("SELECT * FROM nations WHERE name = :name");

        $stmt->bindValue(":name", $name);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getAllNationsFromDatabase() {
        $stmt = $this->pdo->prepare("SELECT * FROM nations");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getOccupiedNationTagsFromMatch(int $idMatch) {
        $stmt = $this->pdo->prepare("SELECT desired_nation_tag FROM players_list WHERE id_match = :id_match");

        $stmt->bindValue(":id_match", $idMatch);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getPlayersNationTagFromMatch(int $playerId, int $matchId)
    {
        $stmt = $this->pdo->prepare("SELECT desired_nation_tag FROM players_list WHERE id_match = :id_match and id_user = :id_player");

        $stmt->bindValue(":id_match", $matchId);
        $stmt->bindValue(":id_player", $playerId);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function changePlayersNationFromMatch(int $playerId, int $matchId, $nationTag) {
        $stmt = $this->pdo->prepare("UPDATE players_list SET desired_nation_tag = :nationTag WHERE id_match = :id_match AND id_user = :id_player");

        $stmt->bindValue(":nationTag", $nationTag);
        $stmt->bindValue(":id_match", $matchId);
        $stmt->bindValue(":id_player", $playerId);

        return $stmt->execute();
    }
}