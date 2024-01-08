<?php

namespace redstar\Controllers;


use redstar\Models\DatabaseModel;
use redstar\Models\UserModel;

class AuthPageController implements IController
{

    public function show(string $pageTitle): array
    {

        $header = new HeaderController();

        $tplData = $header->show($pageTitle);


        // Pokud je uživatel přihlášen, přesměruj ho na hlavní stránku
        if (isset($tplData["user"])) {
            header("Location: index.php");
        }

        if (isset($_GET["part"])) {
            if ($_GET["part"] == "login") {
                $tplData["part"] = "login";
            } else if ($_GET["part"] == "forgot-password") {
                $tplData["part"] = "forgot-password";
            } else {
                $tplData["part"] = "registration";
            }
        }

        if (isset($_POST["register-btn"]) and $_POST["register-btn"] == "register") {
            if ($this->registerUser()) {
                $tplData["register-status"] = "Success";
                $tplData["part"] = "login";
            } else {
                $tplData["register-status"] = "Fail";
            }
        }

        if (isset($_POST["check-username"])) {
            $this->doesUsernameExist($_POST["check-username"], true);
            exit();
        }

        if (isset($_POST["check-email"])) {
            $this->doesEmailExist($_POST["check-email"], true);
            exit();
        }

        return $tplData;
    }

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
        $data["image-name"] = $_POST["image-name"] ?? "";
        $data["role"] = $_POST["role"] ?? 4;

        $db = DatabaseModel::getDatabaseModel();
        return $db->addUserToDatabase($data);
    }

    private function isValidPassword(string $password): bool {
        if (strlen($password) < 6)
            return false;

        if (preg_match('/\\d/', $password) < 1)
            return false;

        return true;
    }
}