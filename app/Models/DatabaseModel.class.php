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

    /** @var PDO $pdo Instance pracující s databází prostřednictvím PDO. */
    private PDO $pdo;

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

    /**
     * Metoda pro získání přihlašovacích údajů uživatele
     * @param string $username - uživatelské jméno uživatele
     * @return mixed - asociativní pole atributů uživatele, jinak pokud příkaz selhal, tak vrací false
     */
    public function getUserCredentials(string $username) {
        $stmt = $this->pdo->prepare("SELECT username, password FROM users WHERE username = :username");

        $stmt->bindValue(":username", $username);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Metoda získá všechny údaje o uživateli dle ID uživatele
     * @param int $id - ID uživatele
     * @return mixed - asociativní pole atributů uživatele, jinak pokud selhal, tak vrací false
     */
    public function getUserDataById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id_user = :id");

        $stmt->bindValue(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Metoda získá všechny údaje o uživateli dle ID uživatele
     * @param string $username - uživatelské jméno uživatele
     * @return mixed - asociativní pole atributů uživatele, jinak pokud selhal, tak vrací false
     */
    public function getUserDataByUsername(string $username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");

        $stmt->bindValue(":username", $username);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Metoda získá všechny údaje o uživateli dle ID uživatele
     * @param string $email - emailová adresa uživatele
     * @return mixed - asociativní pole atributů uživatele, jinak pokud selhal, tak vrací false
     */
    public function getUserDataByEmail(string $email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");

        $stmt->bindValue(":email", $email);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Metoda přidá uživatele do databáze na základě dat
     * @param $data - pole údajů uživatele
     * @return bool - true pokud vše proběhlo úspěšně, jinak false
     */
    public function addUserToDatabase($data): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (username, password, email, first_name, last_name, role_id_role) VALUES (:username, :password, :email, :firstName, :lastName, :role)");

            $stmt->bindValue(":username", $data["username"]);
            $stmt->bindValue(":password", $data["password"]);
            $stmt->bindValue(":email", $data["email"]);
            $stmt->bindValue(":firstName", $data["first-name"]);
            $stmt->bindValue(":lastName", $data["last-name"]);
            $stmt->bindValue(":role", $data["role"]);
            return $stmt->execute();
        } catch (\PDOException $e) {
            echo $e;
            return false;
        }
    }

//    public function getAllUsernamesFromDatabase(): array
//    {
//        $stmt = $this->pdo->prepare("SELECT username FROM users");
//
//        $stmt->execute();
//
//        $data = array();
//
//        for ($i = 0; $row = $stmt->fetch(PDO::FETCH_NUM); $i++) {
//            $data[$i] = $row[0];
//        }
//
//        return $data;
//    }
//
//    public function getAllEmailsFromDatabase(): array
//    {
//        $stmt = $this->pdo->prepare("SELECT email FROM users");
//
//        $stmt->execute();
//
//        $data = array();
//
//        for ($i = 0; $row = $stmt->fetch(PDO::FETCH_NUM); $i++) {
//            $data[$i] = $row[0];
//        }
//
//        return $data;
//    }

    /**
     * Získá všechny atributy o kampani dle ID
     * @param int $id - ID kampaně
     * @return mixed - vrací asociativní pole atributů kampaně, nebo false, pokud příkaz selhal
     */
    public function getMatchByIdFromDatabase(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM matches WHERE id_match = :id_match");

        $stmt->bindValue(":id_match", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Získá všechny kampaně z databáze a jejich atributy
     * @return array - vrací asociativní pole s kampaněmi a jejich atributy
     */
    public function getAllMatchesFromDatabase(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM matches ORDER BY date_created DESC ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

//    public function getMatchesCount() {
//        $stmt = $this->pdo->prepare("SELECT COUNT(id_match) FROM matches");
//        $stmt->execute();
//
//        return $stmt->fetch()[0];
//    }

    /**
     * Získá aktuální ID hráčů kampaně dle ID kampaně
     * @param int $matchId - ID kampaně
     * @return array - pole s ID uživatelů
     */
    public function getMatchPlayerIdsFromDatabase(int $matchId): array {
        $stmt = $this->pdo->prepare("SELECT id_user FROM players_list WHERE id_match = :id_match");

        $stmt->bindValue(":id_match", $matchId);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Získá aktuální ID vyhozených hráčů kampaně dle ID kampaně
     * @param int $matchId - ID kampaně
     * @return array - pole s ID uživateli
     */
    public function getMatchBannedPlayerIdsFromDatabase(int $matchId): array {
        $stmt = $this->pdo->prepare("SELECT id_user FROM banned_players_from_matches WHERE id_match = :id_match");

        $stmt->bindValue(":id_match", $matchId);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Získá všechny atributy role dle ID
     * @param int $id - ID role
     * @return mixed po úspěšném příkazu vrací asociativní pole atributů role, jinak false
     */
    public function getRoleByIdFromDatabase(int $id): mixed
    {
        $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE id_role = :id_role");

        $stmt->bindValue(":id_role", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Přidá hráče do kampaně
     * @param int $playerId - ID hráče
     * @param int $matchId - ID kampaně
     * @return bool - true pokud vše proběhlo úspěšně, jinak false
     */
    public function addPlayerToMatch(int $playerId, int $matchId): bool {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO players_list (id_match, id_user) VALUES (:id_match, :id_player)");

            $stmt->bindValue(":id_match", $matchId);
            $stmt->bindValue(":id_player", $playerId);

            return $stmt->execute();
        } catch (\PDOException) {
            return false;
        }
    }

    /**
     * Odebere hráče z kampaně
     * @param int $playerId - ID hráče
     * @param int $matchId - ID kampaně
     * @return bool - true pokud vše proběhlo úspěšně, jinak false
     */
    public function removePlayerFromMatch(int $playerId, int $matchId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM players_list WHERE (id_match = :id_match and id_user = :id_player)");

        $stmt->bindValue(":id_match", $matchId);
        $stmt->bindValue(":id_player", $playerId);

        return $stmt->execute();
    }

    /**
     * Zakáže přístup hráči z kampaně
     * @param int $playerId - ID hráče
     * @param int $matchId - ID kampaně
     * @return bool - true pokud vše proběhlo úspěšně, jinak false
     */
    public function banPlayerFromMatch(int $playerId, int $matchId): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO banned_players_from_matches (id_user, id_match) VALUES (:id_player, :id_match)");

            $stmt->bindValue(":id_match", $matchId);
            $stmt->bindValue(":id_player", $playerId);

            return $stmt->execute();
        } catch (\PDOException) {
            return false;
        }
    }

    /**
     * Povolí přístup hráči ke kampani
     * @param int $playerId - ID hráče
     * @param int $matchId - ID kampaně
     * @return bool - true pokud vše proběhlo úspěšně, jinak false
     */
    public function unbanPlayerFromMatch(int $playerId, int $matchId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM banned_players_from_matches WHERE (id_match = :id_match and id_user = :id_player)");

        $stmt->bindValue(":id_match", $matchId);
        $stmt->bindValue(":id_player", $playerId);

        return $stmt->execute();
    }

    /**
     * Získá všechny atributy o zemi dle TAGu
     * @param string $tag - TAG země
     * @return mixed - asociativní pole atributů země pokud vše proběhlo úspěšně, jinak false
     */
    public function getNationByTag(string $tag) {
        $stmt = $this->pdo->prepare("SELECT * FROM nations WHERE tag = :tag");

        $stmt->bindValue(":tag", $tag);
        $stmt->execute();

        return $stmt->fetch();
    }


    /**
     * Získá všechny atributy o zemi dle názvu
     * @param string $name - název země
     * @return mixed - asociativní pole atributů země pokud vše proběhlo úspěšně, jinak false
     */
    public function getNationByName(string $name) {
        $stmt = $this->pdo->prepare("SELECT * FROM nations WHERE name = :name");

        $stmt->bindValue(":name", $name);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Získá všechny národy a jejich atributy z databáze
     * @return array - pole s národy a jejich atributy
     */
    public function getAllNationsFromDatabase(): array{
        $stmt = $this->pdo->prepare("SELECT * FROM nations");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Získá již obsazené země hráči dle ID kampaně
     * @param int $idMatch - ID kampaně
     * @return array - pole s národy a jejich atributy
     */
    public function getOccupiedNationTagsFromMatch(int $idMatch): array {
        $stmt = $this->pdo->prepare("SELECT desired_nation_tag FROM players_list WHERE id_match = :id_match");

        $stmt->bindValue(":id_match", $idMatch);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Získá TAG národu, který si uživatel zvolil v dané kampani
     * @param int $playerId - ID hráče
     * @param int $matchId - ID kampaně
     * @return mixed - asociativní pole s TAGem země pokud vše proběhlo úspěšně, jinak false
     */
    public function getPlayersNationTagFromMatch(int $playerId, int $matchId)
    {
        $stmt = $this->pdo->prepare("SELECT desired_nation_tag FROM players_list WHERE id_match = :id_match and id_user = :id_player");

        $stmt->bindValue(":id_match", $matchId);
        $stmt->bindValue(":id_player", $playerId);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Změní vybraný národ hráči dané kampaně
     * @param int $playerId - ID hráče
     * @param int $matchId - ID kampaně
     * @param $nationTag - TAG národa
     * @return bool - true pokud vše proběhlo úspěšně, jinak false
     */
    public function changePlayersNationFromMatch(int $playerId, int $matchId, $nationTag): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE players_list SET desired_nation_tag = :nationTag WHERE id_match = :id_match AND id_user = :id_player");

            $stmt->bindValue(":nationTag", $nationTag);
            $stmt->bindValue(":id_match", $matchId);
            $stmt->bindValue(":id_player", $playerId);

            return $stmt->execute();
        } catch (\PDOException) {
            return false;
        }
    }

    /**
     * Získá všechny uživatele a jejich atributy z databáze
     * @return array pole s uživateli a jejich atributy
     */
    public function getAllUsers(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Zabanuje kompletně uživatele z aplikace
     * @param int $id - ID uživatele
     * @return bool - true pokud vše proběhlo úspěšně, jinak false
     */
    public function banUserById(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET is_banned = 1 WHERE id_user = :id_user");

        $stmt->bindValue(":id_user", $id);

        return $stmt->execute();
    }

    /**
     * Odbanuje kompletně uživatele z aplikace
     * @param int $id - ID uživatele
     * @return bool - true pokud vše proběhlo úspěšně, jinak false
     */
    public function unbanUserById(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET is_banned = 0 WHERE id_user = :id_user");

        $stmt->bindValue(":id_user", $id);

        return $stmt->execute();
    }

    /**
     * Smaže kampaň z databáze
     * @param int $matchId - ID kampaně
     * @return bool - true pokud vše proběhlo úspěšně, jinak false
     */
    public function removeMatchFromDatabase(int $matchId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM matches WHERE id_match = :id_match");
        $stmt->bindValue(":id_match", $matchId);

        return $stmt->execute();
    }

    /**
     * Smaže uživatele z databáze
     * @param int $userId - ID uživatele
     * @return bool - true pokud vše proběhlo úspěšně, jinak false
     */
    public function removeUserFromDatabase(int $userId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id_user = :id_user");
        $stmt->bindValue(":id_user", $userId);

        return $stmt->execute();
    }

    /**
     * Získá hráčovo umístění dané kampaně
     * @param $playerId - ID uživatele
     * @param $matchId - ID kampaně
     * @return mixed - asociativní pole s umístěním uživatele pokud vše proběhlo úspěšně, jinak false
     */
    public function getPlayersStatusFromMatch($playerId, $matchId)
    {
        $stmt = $this->pdo->prepare("SELECT status FROM players_list WHERE id_user = :id_user AND id_match = :id_match");
        $stmt->bindValue(":id_user", $playerId);
        $stmt->bindValue(":id_match", $matchId);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Nastaví uživateli umístění dané kampaně
     * @param int $playerId - ID uživatele
     * @param int $matchId - ID kampaně
     * @param string $status - řetězec umístění
     * @return bool - true pokud vše proběhlo úspěšně, jinak false
     */
    public function setPlayerStatusFromMatch(int $playerId, int $matchId, string $status): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE players_list SET status = :status WHERE id_match = :id_match and id_user = :id_player");
            $stmt->bindValue(":status", $status);
            $stmt->bindValue(":id_match", $matchId);
            $stmt->bindValue(":id_player", $playerId);

            return $stmt->execute();
        } catch (\PDOException) {
            return false;
        }
    }

    /**
     * Získá role a jejich atributy z databáze
     * @return array - pole atributů rolí
     */
    public function getAllRolesFromDatabase(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM roles");
//        $stmt->bindValue(":roles", "roles");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Změní roli uživatele
     * @param int $userId - ID uživatele
     * @param int $roleId - ID role
     * @return bool - true pokud vše proběhlo úspěšně, jinak false
     */
    public function changeUserRole(int $userId, int $roleId): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET role_id_role = :id_role WHERE id_user = :id_user");
            $stmt->bindValue(":id_role", $roleId);
            $stmt->bindValue(":id_user", $userId);

            return $stmt->execute();
        } catch (\PDOException) {
            return false;
        }
    }

    public function changeMatchJoiningCredentials(int $matchId, string $joinCode, string $joinPassword): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE matches SET join_code = :join_code, join_password = :join_password WHERE id_match = :id_match");
            $stmt->bindValue(":id_match", $matchId);
            $stmt->bindValue(":join_code", $joinCode);
            $stmt->bindValue(":join_password", $joinPassword);
            return $stmt->execute();
        } catch (\PDOException) {
            return false;
        }
    }

    public function setMatchFinishDate(int $matchId, string $dateTime): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE matches SET date_finished = :date_finished WHERE id_match = :id_match");
            $stmt->bindValue(":id_match", $matchId);
            $stmt->bindValue(":date_finished", $dateTime);
            return $stmt->execute();
        } catch (\PDOException) {
            return false;
        }
    }

    public function createNewMatch(string $name, string $description, int $ownerId, int $maxPlayers, string $dateCreated, string $dateStarting, string $imageName): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO matches (owner_id_user, name, description, max_players, date_created, date_starting, image_name) VALUES (:id_user, :name, :description, :max_players, :date_created, :date_starting, :image_name)");
            $stmt->bindValue(":id_user", $ownerId);
            $stmt->bindValue(":name", $name);
            $stmt->bindValue(":description", $description);
            $stmt->bindValue(":max_players", $maxPlayers);
            $stmt->bindValue(":date_created", $dateCreated);
            $stmt->bindValue(":date_starting", $dateStarting);
            $stmt->bindValue(":image_name", $imageName);
            return $stmt->execute();
        } catch (\PDOException) {
            return false;
        }
    }
}