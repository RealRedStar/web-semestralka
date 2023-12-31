<?php

namespace redstar\Controllers;


use redstar\Models\DatabaseModel;
use redstar\Models\UserModel;

class AuthPageController implements IController
{

    public function show(string $pageTitle): array
    {
        // TODO: Implement show() method.

        $tplData = [];

        $header = new HeaderController();

        $tplData = $header->show($pageTitle);


        // Pokud je uživatel přihlášen, přesměruj ho na hlavní stránku
        if (isset($tplData["user"])) {
            header("Location: index.php");
        }

        if (isset($_POST["register-btn"]) and $_POST["register-btn"] == "register") {
            if ($this->registerUser()) {
                $tplData["register-status"] = "Success";
            } else {
                $tplData["register-status"] = "Fail";
            }
        }

        if (isset($_POST["usernames"]) and $_POST["usernames"] == "load") {
            $this->loadUsernames();
            exit();
        }

        if (isset($_POST["emails"]) and $_POST["emails"] == "load") {
            $this->loadEmails();
            exit();
        }

        return $tplData;
    }

    public function loadUsernames() {
        $data = UserModel::getAllUsernames();
        echo json_encode($data);
    }

    public function loadEmails() {
        $data = UserModel::getAllEmails();
        echo json_encode($data);
    }

    public function registerUser(): bool {
        $data = array();

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
}