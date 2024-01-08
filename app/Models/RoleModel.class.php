<?php

namespace redstar\Models;

use http\Client\Curl\User;

class RoleModel implements \JsonSerializable
{
    private int $id;
    private string $name;
    private int $permissions;

    /**
     * @param int $id
     * @param string $name
     * @param int $permissions
     */
    public function __construct(int $id, string $name, int $permissions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->permissions = $permissions;
    }

    public static function getRoleById(int $id): ?RoleModel {
        $db = DatabaseModel::getDatabaseModel();

        $data = $db->getRoleByIdFromDatabase($id);

        if (!isset($data["id_role"]))
            return null;

        $id = $data["id_role"];
        $name = $data["name"];
        $permissions = $data["permissions"];

        return new RoleModel($id, $name, $permissions);
    }

    public static function getAllRoles(): array
    {
        $db = DatabaseModel::getDatabaseModel();

        $data = $db->getAllRolesFromDatabase();

        $roles = array();

        for ($i = 0; $i < sizeof($data); $i++) {
            $id = $data[$i]["id_role"];
            $name = $data[$i]["name"];
            $permissions = $data[$i]["permissions"];
            $roles[$i] = new RoleModel($id, $name, $permissions);
        }

        return $roles;
    }

    public static function changeUserRole(int $userId, int $roleId)
    {
        $db = DatabaseModel::getDatabaseModel();

        $user = UserModel::getUserById($userId);
        $role = self::getRoleById($roleId);

        if (!isset($user)) {
            return;
        }

        if (!isset($role)) {
            return;
        }

        $db->changeUserRole($userId, $roleId);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPermissions(): int
    {
        return $this->permissions;
    }


    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => $this->permissions
        ];
    }
}