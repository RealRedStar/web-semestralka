<?php

namespace redstar\Controllers;

use redstar\Models\MatchModel;
use redstar\Models\NationModel;
use redstar\Models\UserModel;

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

        if (isset($_POST["remove-match-btn"])) {
            MatchModel::removeMatch($_POST["remove-match-btn"]);
            $tplData["success"] = "Kampaň byla úspěšně odstraněna.";
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

        try {
            $match = $this->retrieveMatch($_GET["match-id"]);
        } catch (\Error) {
            $tplData["error"] = "Nastala chyba při načítání kampaně";
            return $tplData;
        }

        if (!isset($match)) {
            $tplData["error"] = "Kampaň nebyla nalezena";
            return $tplData;
        } else {
            $tplData["match"] = $match;
        }


        if (isset($_POST["join-btn"])) {
            $this->addPlayerToMatch($_POST["join-btn"], $match->getId());
            $match = $this->retrieveMatch($_GET["match-id"]);
            $tplData["match"] = $match;
        }

        if (isset($_POST["leave-btn"])) {
            $this->removePlayerFromMatch($_POST["leave-btn"], $match->getId());
            $match = $this->retrieveMatch($_GET["match-id"]);
            $tplData["match"] = $match;
        }

        if (isset($_POST["ban-player"])) {
            $this->banPlayerFromMatch($_POST["ban-player"], $match->getId());
            $match = $this->retrieveMatch($_GET["match-id"]);
            $tplData["match"] = $match;
            exit();
        }

        if (isset($_POST["unban-player"])) {
            $this->unbanPlayerFromMatch($_POST["unban-player"], $match->getId());
            $match = $this->retrieveMatch($_GET["match-id"]);
            $tplData["match"] = $match;
            exit();
        }

        if (in_array($tplData["user"], $match->getBannedPlayers()) and $tplData["user"]->getRole()->getPermissions() < 5 and $match->getOwner() != $tplData["user"]) {
            $tplData["error"] = "Byli jste vyhozeni z této kampaně";
            $tplData["match"] = null;
            return $tplData;
        }

        if (in_array($tplData["user"], $match->getPlayers())) {
            $tplData["user-is-in-game"] = true;
        } else {
            $tplData["user-is-in-game"] = false;
        }

        $availableNations = NationModel::getAvailableNationsFromMatch($match->getId());
        $tplData["available-nations"] = $availableNations;

        if (isset($_POST["change-nation"])) {
            if (!isset($_POST["nation-name"]) or !isset($_POST["player-id"]) or !isset($match)) {
                exit();
            }
            $nation = NationModel::getNationByName($_POST["nation-name"]);
            if (in_array($nation, $availableNations)) {
                $this->changePlayersDesiredNation($_POST["player-id"], $match->getId(), $_POST["nation-name"]);
            }
            exit();
        }

        if (isset($_POST["change-status"])) {
            if (!isset($_POST["status"]) or !isset($_POST["player-id"]) or !isset($match)) {
                exit();
            }
            MatchModel::setPlayerStatusFromMatch($_POST["player-id"], $match->getId(), $_POST["status"]);
        }

        if (isset($_GET["edit"]) and $_GET["edit"] == "true") {
            $tplData["edit"] = true;
        }

        return $tplData;
    }

    private function retrieveMatch(int $id): ?MatchModel {
        return MatchModel::getMatchById($id);
    }

    private function addPlayerToMatch(int $playerId, int $matchId) {
        MatchModel::addPlayerToMatch($playerId, $matchId);
    }

    private function removePlayerFromMatch(int $playerId, int $matchId) {
        MatchModel::removePlayerFromMatch($playerId, $matchId);
    }

    private function banPlayerFromMatch(int $playerId, int $matchId) {
        MatchModel::banPlayerFromMatch($playerId, $matchId);
    }

    private function unbanPlayerFromMatch(int $playerId, int $matchId) {
        MatchModel::unbanPlayerFromMatch($playerId, $matchId);
    }

    private function changePlayersDesiredNation(int $playerId, int $matchId, string $nationName) {
        NationModel::changePlayersNationFromMatch($playerId, $matchId, $nationName);
    }

}