<?php

namespace redstar\Controllers;

use redstar\Models\UserModel;

class UsersPageController implements IController
{

    public function show(string $pageTitle): array
    {
        $header = new HeaderController();

        $tplData = $header->show($pageTitle);

        if (!isset($tplData["user"])) {
            $tplData["error"] = "Pro zobrazení této stránky se musíte přihlásit!";
            return $tplData;
        }

        if ($tplData["user"]->getRole()->getPermissions() < 5) {
            $tplData["error"] = "Nemáte oprávnění zobrazit tuto stránku!";
            return $tplData;
        }

        if (isset($_POST["ban-user"])) {
            UserModel::banUserById($_POST["ban-user"]);
            exit();
        }

        if (isset($_POST["unban-user"])) {
            UserModel::unbanUserById($_POST["unban-user"]);
            exit();
        }

        if (isset($_POST["completely-remove-user"])) {
            UserModel::completelyRemoveUser($_POST["completely-remove-user"]);
            exit();
        }

        $tplData["users"] = UserModel::getAllUsers();

        return $tplData;
    }
}