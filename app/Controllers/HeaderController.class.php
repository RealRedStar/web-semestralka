<?php

namespace redstar\Controllers;

use redstar\Models\DatabaseModel;
use redstar\Models\UserModel;

/**
 * Tato třída reprezentuje controller, který se stará o získání dat pro šablonu s hlavičkou
 */
class HeaderController implements IController
{

    /**
     * Zajistí vypsání dané stránky
     * @param string $pageTitle titulek stránky
     * @return array pole dat pro šablonu
     */
    public function show(string $pageTitle): array
    {
        session_start();
        $tplData = [];

        $tplData["title"] = $pageTitle;

        // pokud se uživatel odhlásil, ukončíme relaci
        if (isset($_POST["logout-btn"]) and $_POST["logout-btn"] == "logout") {
            $tplData["login-status"] = "Logout";
            session_unset();
        }

        // pokud se uživatel snaží přihlásit, zkontrolujeme zadané údaje
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

        // pokud je nastaven v relaci uživatel, přihlásíme ho
        if (isset($_SESSION["user"])) {
            // naopak pokud je uživatel bannutý, ukončíme relaci
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