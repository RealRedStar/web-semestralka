<?php

namespace redstar\Controllers;

use redstar\Models\DatabaseModel;
use redstar\Models\UserModel;

class HeaderController implements IController
{
    private DatabaseModel $db;

    public function __construct()
    {
        $db = DatabaseModel::getDatabaseModel();
    }

    public function show(string $pageTitle): array
    {
        session_start();
        $tplData = [];

        $tplData["title"] = $pageTitle;


        if (isset($_POST["logout-btn"]) and $_POST["logout-btn"] == "logout") {
            $tplData["login-status"] = "Logout";
            session_unset();
        }

        if (isset($_POST["login-btn"]) and $_POST["login-btn"] == "login") {
            if (!isset($_POST["username"]) or !isset($_POST["password"])) {
                $tplData["login-status"] = "Fail";
            } elseif (!$this->checkCredentials($_POST["username"], $_POST["password"])) {
                $tplData["login-status"] = "Fail";
            } else {
                $user = UserModel::getUserByUsername($_POST["username"]);
                if ($user->isBanned()) {
                    $tplData["login-status"] = "Banned";
                } else {
                    $tplData["login-status"] = "Success";
                    $_SESSION["user"] = UserModel::getUserByUsername($_POST["username"]);
                }
            }
        }

        if (isset($_SESSION["user"])) {
            if ($_SESSION["user"]->isBanned()) {
                $tplData["user"] = null;
                $tplData["login-status"] = "Banned";
                session_unset();
            } else {
                $tplData["user"] = $_SESSION["user"];
            }
        } else {
            $tplData["user"] = null;
        }

        return $tplData;

    }

    /**
     * Metoda pro ověření přihlašovacích údajů
     * @param string $username uživ. jméno
     * @param string $password heslo
     * @return bool true pokud jsou údaje správné, false pokud nejsou
     */
    public function checkCredentials(string $username, string $password): bool{
        $db = DatabaseModel::getDatabaseModel();
        $data = $db->getUserCredentials($username);
        try {
            if (count($data) < 1) {
                return false;
            }

            if ($username == $data["username"] and password_verify($password, $data["password"])) {
                return true;
            }
        } catch (\Error $e) {
            return false;
        }

        return false;
    }
}