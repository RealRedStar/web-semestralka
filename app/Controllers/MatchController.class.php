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

        if (isset($_POST["save-match"])) {
            try {
                if (!isset($_POST["match-name"]) or !isset($_POST["match-max-players"]) or !isset($_POST["match-date-starting"])) {
                    $tplData["error"] = "Kampaň se nepodařilo vytvořit, nezadali jste povinné údaje";
                    return $tplData;
                }

                if ($_POST["match-max-players"] < 2 or $_POST["match-max-players"] > 32) {
                    $tplData["error"] = "Kampaň se nepodařilo vytvořit, chybně zadaný maximální počet hráčů.";
                }
                $name = $_POST["match-name"];
                $description = $_POST["match-description"] ?? "";
                $ownerId = $tplData["user"]->getId();
                $maxPlayers = $_POST["match-max-players"];
                $dateTime = new \DateTime('now');
                $dateCreated = $dateTime->format("Y-m-d H:i:s");
                $dateStarting = \DateTime::createFromFormat('Y-m-d\TH:i', $_POST["match-date-starting"])->format("Y-m-d H:i:s");
                $imageName = "";

                if ($dateStarting <= $dateCreated) {
                    $tplData["error"] = "Kampaň se nepodařilo vytvořit, nesprávný datum zahájení.";
                    return $tplData;
                }

                if (isset($_FILES["match-image"]) and $_FILES["match-image"]["name"] != "") {
                    $isImage = getimagesize($_FILES["match-image"]["tmp_name"]);
                    if (!$isImage) {
                        $tplData["error"] = "Kampaň se nepodařilo vytvořit, nepodařilo se zpracovat obrázek.";
                        return $tplData;
                    }

                    if ($_FILES["match-image"]["size"] > 5000000) {
                        $tplData["error"] = "Kampaň se nepodařilo vytvořit, obrázek je příliš velký.";
                        return $tplData;
                    }

                    $targetDir = realpath(__DIR__ . "/../Resources/user-images/matches/") . "/";

//                    "<script src='../../../web-semestralka/app/Resources/user-images/matches/'></script>"

                    $username = $tplData["user"]->getUsername();

                    $shortImageName =  $username . "_match_1" . "." . pathinfo($_FILES["match-image"]["name"], PATHINFO_EXTENSION);;
                    $targetName = $targetDir . $shortImageName;

                    $index = 2;
                    while (file_exists($targetName)) {
                        $targetName = $targetDir . $username . "_match_" . $index . "." . pathinfo($_FILES["match-image"]["name"], PATHINFO_EXTENSION);
                        $index++;
                    }

                    if (!move_uploaded_file($_FILES["match-image"]["tmp_name"], $targetName)) {
                        $tplData["error"] = "Kampaň se nepodařilo vytvořit, nepodařilo se zpracovat obrázek.";
                        return $tplData;
                    }

                    $imageName = $shortImageName;
                }

                if (!MatchModel::saveNewMatch($name, $description, $ownerId, $maxPlayers, $dateCreated, $dateStarting, $imageName)) {
                    $tplData["error"] = "Kampaň se nepodařilo vytvořit, nespecifikovaná chyba.";
                    return $tplData;
                } else {
                    $tplData["success"] = "Kampaň byla úspěšně vytvořená";
                    return $tplData;
                }

            } catch (\Error $e) {
                echo $e;
                $tplData["error"] = "Kampaň se nepodařilo vytvořit, nespecifikovaná chyba.";
                return $tplData;
            }
        }

        if (!isset($_GET["match-id"])) {
            $tplData["error"] = "Kampaň nebyla specifikována";
            return $tplData;
        }

        if ($_GET["match-id"] == "new") {
            $tplData["match"] = new MatchModel(-1, $tplData["user"], "", "", 2, "", "", "", "", "", "", array(), array());
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

        if (isset($_POST["start-match"])) {
            if (isset($_POST["join-code"]) and isset($_POST["join-password"])) {
                $this->startMatch($match->getId(), $_POST["join-code"], $_POST["join-password"]);
                $match = $this->retrieveMatch($_GET["match-id"]);
                $tplData["match"] = $match;
            }
        }

        if (isset($_POST["stop-match"])) {
            $this->stopMatch($match->getId());
            $match = $this->retrieveMatch($_GET["match-id"]);
            $tplData["match"] = $match;
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

    private function startMatch(int $matchId, string $joinCode, string $joinPassword)
    {
        MatchModel::changeJoiningCredentials($matchId, $joinCode, $joinPassword);
    }

    private function stopMatch(int $matchId)
    {
        $dateTime = new \DateTime('now');
        $formattedDateTime = $dateTime->format("Y-m-d H:i:s");

        MatchModel::setMatchFinishDate($matchId, $formattedDateTime);
    }

}