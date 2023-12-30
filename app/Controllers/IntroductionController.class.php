<?php

namespace redstar\Controllers;

use redstar\Models\DatabaseModel;

/**
 * Ovladač zajišťující vypsání úvodní stránky
 */
class IntroductionController implements IController
{
    private $db;

    public function __construct()
    {
        $this->db = DatabaseModel::getDatabaseModel();
    }

    public function show(string $pageTitle): array
    {

        $tplData = [];

        $tplData["title"] = $pageTitle;

        if (isset($_POST["logout-btn"]) and $_POST["logout-btn"] == "logout") {
            $tplData["login-status"] = "Logout";
            $_POST = array();
        }

        if (isset($_POST["login-btn"]) and $_POST["login-btn"] == "login") {
            if (!isset($_POST["username"]) or !isset($_POST["password"])) {
                $tplData["login-status"] = "Fail";
            } elseif (!$this->checkCredentials($_POST["username"], $_POST["password"])) {
                $tplData["login-status"] = "Fail";
            } else {
                $tplData["login-status"] = "Success";
            }
        }

        return $tplData;
    }

    public function checkCredentials(string $username, string $password): bool{
        $data = $this->db->getUserCredentials($username);
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