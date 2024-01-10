<?php

namespace redstar\Controllers;

use redstar\Models\RoleModel;
use redstar\Models\UserModel;

/**
 * Tato třída reprezentuje controller, který se stará o získání dat pro šablonu se seznamem kampaní
 */
class UsersPageController implements IController
{

    /**
     * Zajistí vypsání dané stránky
     * @param string $pageTitle titulek stránky
     * @return array pole dat pro šablonu
     */
    public function show(string $pageTitle): array
    {
        $header = new HeaderController();

        $tplData = $header->show($pageTitle);

        // kontrola zda je uživatel přihlášen
        if (!isset($tplData["user"])) {
            $tplData["error"] = "Pro zobrazení této stránky se musíte přihlásit!";
            return $tplData;
        }

        // kontrola zda má uživatel oprávnění
        if ($tplData["user"]->getRole()->getPermissions() < 5) {
            $tplData["error"] = "Nemáte oprávnění zobrazit tuto stránku!";
            return $tplData;
        }

        // akce pro zabanování uživatele
        if (isset($_POST["ban-user"])) {
            UserModel::banUserById($_POST["ban-user"]);
            exit();
        }

        // akce pro odbanování uživatele
        if (isset($_POST["unban-user"])) {
            UserModel::unbanUserById($_POST["unban-user"]);
            exit();
        }

        // akce pro změnu role uživatele
        if (isset($_POST["change-role"])) {
            RoleModel::changeUserRole($_POST["user-id"], $_POST["role-id"]);
            exit();
        }

        // akce pro kompletní odstranění uživatele z databáze
        if (isset($_POST["completely-remove-user"])) {
            UserModel::completelyRemoveUser($_POST["completely-remove-user"]);
            exit();
        }

        $tplData["users"] = UserModel::getAllUsers();
        $tplData["roles"] = RoleModel::getAllRoles();

        return $tplData;
    }
}