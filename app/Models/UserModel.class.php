<?php

namespace redstar\Models;

class UserModel implements \JsonSerializable
{
    /** @var int ID uživatele */
    private int $id;
    /** @var string uživatelské jméno */
    private string $username;
    /** @var string heslo */
    private string $password;
    /** @var string email */
    private string $email;
    /** @var string křestní jméno */
    private string $firstName;
    /** @var string příjmení */
    private string $lastName;
    /** @var RoleModel role uživatele */
    private RoleModel $role;
    /** @var bool zda je bannutý */
    private bool $isBanned;


    /**
     * @param int $id id uživatele
     * @param string $username uživatelské jméno
     * @param string $password heslo
     * @param string $email email
     * @param string $firstName křestní jméno
     * @param string $lastName příjmení
     * @param RoleModel $role role
     * @param bool $isBanned zda je bannutý
     */
    public function __construct(int $id, string $username, string $password, string $email, string $firstName, string $lastName, RoleModel $role, bool $isBanned)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->role = $role;
        $this->isBanned = $isBanned;
    }

    /**
     * Vrátí uživatele dle hledaného ID
     * @param int $id ID uživatele
     * @return UserModel|null uživatele, pokud existuje uživatel pod daným ID, jinak null
     */
    public static function getUserById(int $id): ?UserModel {
        $db = DatabaseModel::getDatabaseModel();
        $data = $db->getUserDataById($id);

        if (!isset($data["id_user"])) {
            return null;
        } else {
            $id = $data["id_user"];
            $username = $data["username"] ?? "";
            $password = $data["password"] ?? "";
            $email = $data["email"] ?? "";
            $firstName = $data["first_name"] ?? "";
            $lastName = $data["last_name"] ?? "";
            $role = RoleModel::getRoleById($data["role_id_role"]);
            $isBanned = $data["is_banned"] == 1;
        }

        return new UserModel($id, $username, $password, $email, $firstName, $lastName, $role, $isBanned);
    }

    /**
     * Vrátí uživatele dle uživatelského jména
     * @param string $username uživatelské jméno
     * @return UserModel|null uživatele, pokud existuje uživatel, jinak null
     */
    public static function getUserByUsername(string $username): ?UserModel
    {
        $db = DatabaseModel::getDatabaseModel();
        $data = $db->getUserDataByUsername($username);

        if (!isset($data["id_user"])) {
            return null;
        } else {
            $id = $data["id_user"];
            $username = $data["username"] ?? "";
            $password = $data["password"] ?? "";
            $email = $data["email"] ?? "";
            $firstName = $data["first_name"] ?? "";
            $lastName = $data["last_name"] ?? "";
            $role = RoleModel::getRoleById($data["role_id_role"]);
            $isBanned = $data["is_banned"] == 1;
        }

        return new UserModel($id, $username, $password, $email, $firstName, $lastName, $role, $isBanned);
    }

    /**
     * Kompletně smaže všechny informace o uživateli včetně odehraných a vlastněných zápasů
     * @param int $id ID uživatele
     */
    public static function completelyRemoveUser(int $id) {
        $user = self::getUserById($id);

        $db = DatabaseModel::getDatabaseModel();

        // získáme všechny kampaně
        $matches = MatchModel::getAllMatches();

        // procházíme všechny kampaně a zjišťujeme, zda do ní uživatel nezapadá
        foreach ($matches as $match) {

            // odebereme ho z tabulky hráčů
            if (in_array($user, $match->getPlayers())) {
                MatchModel::removePlayerFromMatch($user->getId(), $match->getId());
            }

            // odebereme ho z tabulky bannutých hráčů
            if (in_array($user, $match->getBannedPlayers())) {
                MatchModel::unbanPlayerFromMatch($user->getId(), $match->getId());
            }

            // odebereme jeho zápas
            if ($match->getOwner() == $user) {
                MatchModel::removeMatch($match->getId());
            }
        }

        // odebereme uživatele z databáze
        $db->removeUserFromDatabase($user->getId());
    }

    /**
     * Získá údaje všech uživatelů a vrátí je
     * @return array pole uživatelů
     */
    public static function getAllUsers(): array
    {
        $db = DatabaseModel::getDatabaseModel();
        $data = $db->getAllUsers();

        $users = array();

        for ($i = 0; $i < sizeof($data); $i++) {
            $id = $data[$i]["id_user"];
            $username = $data[$i]["username"] ?? "";
            $password = $data[$i]["password"] ?? "";
            $email = $data[$i]["email"] ?? "";
            $firstName = $data[$i]["first_name"] ?? "";
            $lastName = $data[$i]["last_name"] ?? "";
            $role = RoleModel::getRoleById($data[$i]["role_id_role"]);
            $isBanned = $data[$i]["is_banned"] == 1;
            $users[$i] = new UserModel($id, $username, $password, $email, $firstName, $lastName, $role, $isBanned);
        }

        return $users;
    }

    /**
     * Získá uživatele podle emailu
     * @param string $email email
     * @return UserModel|null uživatele, pokud existuje, jinak null
     */
    public static function getUserByEmail(string $email): ?UserModel {
        $db = DatabaseModel::getDatabaseModel();
        $data = $db->getUserDataByEmail($email);

        if (!isset($data["id_user"])) {
            return null;
        } else {
            $id = $data["id_user"];
            $username = $data["username"] ?? "";
            $password = $data["password"] ?? "";
            $email = $data["email"] ?? "";
            $firstName = $data["first_name"] ?? "";
            $lastName = $data["last_name"] ?? "";
            $role = RoleModel::getRoleById($data["role_id_role"]);
            $isBanned = $data["is_banned"] == 1;
        }

        return new UserModel($id, $username, $password, $email, $firstName, $lastName, $role, $isBanned);
    }

    /**
     * Získá hráče z kampaně
     * @param int $matchId ID kampaně
     * @return array pole hráčů
     */
    public static function getPlayersFromMatchId(int $matchId): array {
        $db = DatabaseModel::getDatabaseModel();

        $ids = $db->getMatchPlayerIdsFromDatabase($matchId);
        $players = array();

        for ($i = 0; $i < sizeof($ids); $i++) {
            $userData = $db->getUserDataById($ids[$i]["id_user"]);

            if (isset($userData)) {
                $id = $userData["id_user"];
                $username = $userData["username"];
                $password = $userData["password"];
                $email = $userData["email"];
                $firstName = $userData["first_name"] ?? "";
                $lastName = $userData["last_name"] ?? "";
                $role = RoleModel::getRoleById($userData["role_id_role"]);
                $isBanned = $userData["is_banned"] == 1;

                $players[$i] = new UserModel($id, $username, $password, $email, $firstName, $lastName, $role, $isBanned);
            }
        }

        return $players;
    }

    /**
     * Získá a vrátí pole bannutých hráčů jednoho zápasu
     * @param int $id ID zapasu
     * @return array pole bannutých hráčů
     */
    public static function getBannedPlayersFromMatchId(int $id): array
    {
        $db = DatabaseModel::getDatabaseModel();

        $ids = $db->getMatchBannedPlayerIdsFromDatabase($id);
        $players = array();

        for ($i = 0; $i < sizeof($ids); $i++) {
            $userData = $db->getUserDataById($ids[$i]["id_user"]);

            if (isset($userData)) {
                $id = $userData["id_user"];
                $username = $userData["username"];
                $password = $userData["password"];
                $email = $userData["email"];
                $firstName = $userData["first_name"] ?? "";
                $lastName = $userData["last_name"] ?? "";
                $role = RoleModel::getRoleById($userData["role_id_role"]);
                $isBanned = $userData["is_banned"] == 1;

                $players[$i] = new UserModel($id, $username, $password, $email, $firstName, $lastName, $role, $isBanned);
            }
        }

        return $players;
    }

    /**
     * Zabanuje uživatele
     * @param int $id ID uživatele
     */
    public static function banUserById(int $id)
    {
        $db = DatabaseModel::getDatabaseModel();

        $db->banUserById($id);
    }

    /**
     * Odbanuje uživatele
     * @param int $id ID uživatele
     */
    public static function unbanUserById(int $id)
    {
        $db = DatabaseModel::getDatabaseModel();

        $db->unbanUserById($id);
    }

    /**
     * Převede model na řetězec json ve formě asociativního pole
     * @return array - json asociativní pole uživatele
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'role' => $this->role,
            'isBanned' => $this->isBanned
        ];
    }

    /**
     * @return int ID uživatele
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string uživatelské jméno
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string heslo
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string křestní jméno
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string příjmení
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return RoleModel roli
     */
    public function getRole(): RoleModel
    {
        return $this->role;
    }

    /**
     * @return bool zda je bannutý
     */
    public function isBanned(): bool
    {
        return $this->isBanned;
    }

}