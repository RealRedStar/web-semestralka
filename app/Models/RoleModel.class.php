<?php

namespace redstar\Models;


class RoleModel implements \JsonSerializable
{
    /** @var int ID role */
    private int $id;
    /** @var string název role */
    private string $name;
    /** @var int hodnota oprávnění */
    private int $permissions;

    /**
     * @param int $id id role
     * @param string $name název role
     * @param int $permissions oprávnění
     */
    public function __construct(int $id, string $name, int $permissions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->permissions = $permissions;
    }

    /**
     * Vrátí roli podle ID
     * @param int $id ID role
     * @return RoleModel|null Roli, pokud se našla role podle hledaného ID, jinak null
     */
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

    /**
     * vrátí pole všech rolí
     * @return array všech rolí
     */
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

    /**
     * Změní uživateli roli
     * @param int $userId ID uživatele
     * @param int $roleId ID role
     */
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

    /**
     * @return int ID role
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string název role
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int oprávnění role
     */
    public function getPermissions(): int
    {
        return $this->permissions;
    }

    /**
     * Převede model na řetězec json ve formě asociativního pole
     * @return array - json asociativní pole role
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => $this->permissions
        ];
    }
}