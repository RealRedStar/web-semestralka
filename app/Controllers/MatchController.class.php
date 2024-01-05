<?php

namespace redstar\Controllers;

use redstar\Models\MatchModel;

class MatchController implements IController
{

    public function show(string $pageTitle): array
    {
        // TODO: Implement show() method.

        $header = new HeaderController();

        $tplData = $header->show($pageTitle);

        if (isset($_POST["load-match"])) {
            echo json_encode($this->retrieveMatch($_POST["load-match"]));
            exit();
        }

        if (!isset($tplData["user"])) {
            $tplData["error"] = "Abyste mohli zobrazit tuto stránku, musíte se přihlásit!";
            return $tplData;
        }

        if (!isset($_GET["match-id"])) {
            $tplData["error"] = "Kampaň nebyla specifikována";
            return $tplData;
        }

        if ($_GET["match-id"] == "new") {
            $tplData["match"] = "new";
            $tplData["edit"] = true;
            return $tplData;
        }

        $match = $this->retrieveMatch($_GET["match-id"]);

        if (!isset($match)) {
            $tplData["error"] = "Kampaň nebyla nalezena";
            return $tplData;
        } else {
            $tplData["match"] = $match;
        }

        if (in_array($tplData["user"], $match->getBannedPlayers()) and $tplData["user"]->getRole()->getPermissions() < 5 and $match->getOwner() != $tplData["user"]) {
            $tplData["error"] = "Byli jste vyhozeni z této kampaně";
            $tplData["match"] = null;
            return $tplData;
        }

        if (isset($_GET["edit"]) and $_GET["edit"] == "true") {
            $tplData["edit"] = true;
        }


        return $tplData;
    }

    private function retrieveMatch(int $id): ?MatchModel {
        return MatchModel::getMatchById($id);
    }

}