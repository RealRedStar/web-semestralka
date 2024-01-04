<?php

namespace redstar\Models;

class UserModel implements \JsonSerializable
{
    private DatabaseModel $db;
    private int $id;
    private string $username;
    private string $password;
    private string $email;
    private string $firstName;
    private string $lastName;
    private string $imageName;
    private RoleModel $role;

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
            'role' => $this->role
        ];
    }


    public function __construct(int $id, string $username, string $password, string $email, string $firstName, string $lastName, string $imageUrl, RoleModel $role)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->imageName = $imageUrl;
        $this->role = $role;
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
        }

        return new UserModel($id, $username, $password, $email, $firstName, $lastName, $imageUrl, $role);
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
        }

        return new UserModel($id, $username, $password, $email, $firstName, $lastName, $imageUrl, $role);
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
            $role = $data["role_id_role"] ?? 0;
        }

        return new UserModel($id, $username, $password, $email, $firstName, $lastName, $imageUrl, $role);
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

    public function getRole(): int
    {
        return $this->role;
    }

}