<?php

namespace redstar\Models;

class UserModel implements \JsonSerializable
{
    private int $id;
    private string $username;
    private string $password;
    private string $email;
    private string $firstName;
    private string $lastName;
    private string $imageName;
    private RoleModel $role;
    private bool $isBanned;

    public static function banUserById(int $id)
    {
        $db = DatabaseModel::getDatabaseModel();

        $db->banUserById($id);
    }

    public static function unbanUserById(int $id)
    {
        $db = DatabaseModel::getDatabaseModel();

        $db->unbanUserById($id);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'imageUrl' => $this->imageName,
            'role' => $this->role,
            'isBanned' => $this->isBanned
        ];
    }


    public function __construct(int $id, string $username, string $password, string $email, string $firstName, string $lastName, string $imageUrl, RoleModel $role, bool $isBanned)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->imageName = $imageUrl;
        $this->role = $role;
        $this->isBanned = $isBanned;
    }

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
            $imageUrl = $data["image_name"] ?? "";
            $role = RoleModel::getRoleById($data["role_id_role"]);
            $isBanned = $data["is_banned"] == 1;
        }

        return new UserModel($id, $username, $password, $email, $firstName, $lastName, $imageUrl, $role, $isBanned);
    }


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
            $imageUrl = $data["image_name"] ?? "";
            $role = RoleModel::getRoleById($data["role_id_role"]);
            $isBanned = $data["is_banned"] == 1;
        }

        return new UserModel($id, $username, $password, $email, $firstName, $lastName, $imageUrl, $role, $isBanned);
    }

    public static function completelyRemoveUser(int $id) {
        $user = self::getUserById($id);

        $db = DatabaseModel::getDatabaseModel();

        $matches = MatchModel::getAllMatches();

        foreach ($matches as $match) {
            MatchModel::removeMatch($match->getId());

            if (in_array($user, $match->getPlayers())) {
                MatchModel::removePlayerFromMatch($user->getId(), $match->getId());
            }

            if (in_array($user, $match->getBannedPlayers())) {
                MatchModel::unbanPlayerFromMatch($user->getId(), $match->getId());
            }

            if ($match->getOwner() == $user) {
                MatchModel::removeMatch($match->getId());
            }
        }

        $db->removeUserFromDatabase($user->getId());
    }

    public function isBanned(): bool
    {
        return $this->isBanned;
    }

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
            $imageUrl = $data[$i]["image_name"] ?? "";
            $role = RoleModel::getRoleById($data[$i]["role_id_role"]);
            $isBanned = $data[$i]["is_banned"] == 1;
            $users[$i] = new UserModel($id, $username, $password, $email, $firstName, $lastName, $imageUrl, $role, $isBanned);
        }

        return $users;
    }

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
            $imageUrl = $data["image_name"] ?? "";
            $role = RoleModel::getRoleById($data["role_id_role"]);
            $isBanned = $data["is_banned"] == 1;
        }

        return new UserModel($id, $username, $password, $email, $firstName, $lastName, $imageUrl, $role, $isBanned);
    }

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
                $imageName = $userData["image_name"] ?? "";
                $role = RoleModel::getRoleById($userData["role_id_role"]);
                $isBanned = $userData["is_banned"] == 1;

                $players[$i] = new UserModel($id, $username, $password, $email, $firstName, $lastName, $imageName, $role, $isBanned);
            }
        }

        return $players;
    }
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
                $imageName = $userData["image_name"] ?? "";
                $role = RoleModel::getRoleById($userData["role_id_role"]);
                $isBanned = $userData["is_banned"] == 1;

                $players[$i] = new UserModel($id, $username, $password, $email, $firstName, $lastName, $imageName, $role, $isBanned);
            }
        }

        return $players;
    }

    public static function getAllUsernames() : array {
        $db = DatabaseModel::getDatabaseModel();
        $data = $db->getAllUsernamesFromDatabase();

        return $data;
    }

    public static function getAllEmails() : array {
        $db = DatabaseModel::getDatabaseModel();
        $data = $db->getAllEmailsFromDatabase();

        return $data;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getImageName(): string
    {
        return $this->imageName;
    }

    public function getRole(): RoleModel
    {
        return $this->role;
    }

}