<?php

namespace redstar\Controllers;


use redstar\Models\DatabaseModel;
use redstar\Models\UserModel;

/**
 * Tato třída reprezentuje controller, který se stará o získání dat pro šablonu s autentizační stránkou
 */
class AuthPageController implements IController
{

    /**
     * Zajistí vypsání dané stránky
     * @param string $pageTitle titulek stránky
     * @return array pole dat pro šablonu
     */
    public function show(string $pageTitle): array
    {

        $header = new HeaderController();

        // získáme data z headeru
        $tplData = $header->show($pageTitle);


        // Pokud je uživatel přihlášen, přesměruj ho na hlavní stránku
        if (isset($tplData["user"])) {
            header("Location: index.php");
        }

        // nastavíme, která část stránky se má vypsat
        if (isset($_GET["part"])) {
            if ($_GET["part"] == "login") {
                $tplData["part"] = "login";
            } else {
                $tplData["part"] = "registration";
            }
        }

        // zkontrolujeme zda uživatel vyslal POST požadavek pro registraci
        if (isset($_POST["register-btn"]) and $_POST["register-btn"] == "register") {
            if ($this->registerUser()) {
                $tplData["register-status"] = "Success";
                $tplData["part"] = "login";
            } else {
                $tplData["register-status"] = "Fail";
            }
        }

        // zkontrolujeme zda zadané uživatelské jméno již existuje
        if (isset($_POST["check-username"])) {
            $this->doesUsernameExist($_POST["check-username"], true);
            exit();
        }

        // zkontrolujeme zda email již existuje
        if (isset($_POST["check-email"])) {
            $this->doesEmailExist($_POST["check-email"], true);
            exit();
        }

        return $tplData;
    }

    /**
     * Metoda pro ověření, zda náhodou již uživatelské jméno existuje
     * @param string $username uživatelské jméno
     * @param bool $ajaxResponse zda bylo vysláno AJAXem
     * @return bool|void vrátí bool hodnotu tehdy pokud se nejedná o AJAX, jinak vykoná echo příkaz bool hodnoty
     */
    public function doesUsernameExist(string $username, bool $ajaxResponse) {
        $data = UserModel::getUserByUsername($username);

        if (!isset($data) or empty($data)) {
            if ($ajaxResponse)
                echo "false";
            else
                return false;
        } else {
            if ($ajaxResponse)
                echo "true";
            else
                return true;
        }
    }

    /**
     * Zkontroluje, zda náhodou již email existuje
     * @param string $email email
     * @param bool $ajaxResponse zda se jedná o AJAX příkaz
     * @return bool|void vrátí bool hodnotu tehdy pokud se nejedná o AJAX, jinak vykoná echo příkaz bool hodnoty
     */
    public function doesEmailExist(string $email, bool $ajaxResponse) {
        $data = UserModel::getUserByEmail($email);

        if (!isset($data) or empty($data)) {
            if ($ajaxResponse)
                echo "false";
            else
                return false;
        } else {
            if ($ajaxResponse)
                echo "true";
            else
                return true;
        }
    }

    /**
     * Registruje uživatele a přidá ho do databáze
     * @return bool
     */
    public function registerUser(): bool {
        $data = array();

        if (empty($_POST["username"]) || $this->doesUsernameExist($_POST["username"], false))
            return false;

        if (empty($_POST["email"]) || !str_contains($_POST["email"], "@") || $this->doesEmailExist($_POST["email"], false))
            return false;

        if (empty($_POST["password"]) || !$this->isValidPassword($_POST["password"]))
            return false;

        $data["username"] = $_POST["username"];
        $data["password"] = password_hash($_POST["password"], PASSWORD_BCRYPT);
        $data["email"] = $_POST["email"];
        $data["first-name"] = $_POST["first-name"] ?? "";
        $data["last-name"] = $_POST["last-name"] ?? "";
        $data["role"] = $_POST["role"] ?? 4;

        $db = DatabaseModel::getDatabaseModel();
        return $db->addUserToDatabase($data);
    }

    /**
     * Zkontroluje, zda je heslo validní
     * @param string $password heslo
     * @return bool true pokud ano, false pokdu ne
     */
    private function isValidPassword(string $password): bool {
        if (strlen($password) < 6)
            return false;

        if (preg_match('/\\d/', $password) < 1)
            return false;

        return true;
    }
}