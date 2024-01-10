<?php

namespace redstar\Controllers;

use redstar\Models\MatchModel;
use redstar\Models\NationModel;

/**
 * Tato třída reprezentuje controller, který se stará o získání dat pro šablonu se stránkou kampaně
 */
class MatchController implements IController
{

    public function show(string $pageTitle): array
    {

        $header = new HeaderController();

        $tplData = $header->show($pageTitle);

//
//        if (isset($_POST["load-match"])) {
//            echo json_encode($this->retrieveMatch($_POST["load-match"]));
//            exit();
//        }


        // pokud není uživatel přihlášený, bude o tom informován
        if (!isset($tplData["user"])) {
            $tplData["error"] = "Abyste mohli zobrazit tuto stránku, musíte se přihlásit!";
            return $tplData;
        }

        // zkontrolování akce pro vymazání kampaně
        if (isset($_POST["remove-match-btn"])) {
            MatchModel::removeMatch($_POST["remove-match-btn"]);
            $tplData["success"] = "Kampaň byla úspěšně odstraněna.";
            return $tplData;
        }

        // uloží kampaň
        if (isset($_POST["save-match"])) {
            try {
                // kontrola povinných údajů
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

                // kontrola data
                if ($dateStarting <= $dateCreated) {
                    $tplData["error"] = "Kampaň se nepodařilo vytvořit, nesprávný datum zahájení.";
                    return $tplData;
                }

                // kontrola obrázku
                if (isset($_FILES["match-image"]) and $_FILES["match-image"]["name"] != "") {
                    $isImage = getimagesize($_FILES["match-image"]["tmp_name"]);
                    if (!$isImage) {
                        $tplData["error"] = "Kampaň se nepodařilo vytvořit, nepodařilo se zpracovat obrázek.";
                        return $tplData;
                    }

                    // kontrola velikosti obrázku
                    if ($_FILES["match-image"]["size"] > 5000000) {
                        $tplData["error"] = "Kampaň se nepodařilo vytvořit, obrázek je příliš velký.";
                        return $tplData;
                    }

                    $targetDir = realpath(__DIR__ . "/../Resources/user-images/matches/") . "/";

                    $username = $tplData["user"]->getUsername();

                    // nastavíme název obrázku
                    $shortImageName =  $username . "_match_1" . "." . pathinfo($_FILES["match-image"]["name"], PATHINFO_EXTENSION);
                    $targetName = $targetDir . $shortImageName;

                    // kontrola zda obrázek již existuje
                    $index = 2;
                    while (file_exists($targetName)) {
                        $shortImageName = $username . "_match_" . $index . "." . pathinfo($_FILES["match-image"]["name"], PATHINFO_EXTENSION);
                        $targetName = $targetDir . $shortImageName;
                        $index++;
                    }
                    // přesunutí obrázku
                    if (!move_uploaded_file($_FILES["match-image"]["tmp_name"], $targetName)) {
                        $tplData["error"] = "Kampaň se nepodařilo vytvořit, nepodařilo se zpracovat obrázek.";
                        return $tplData;
                    }

                    $imageName = $shortImageName;
                }

                // vytvoření kampaně
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

        // kontrola, zda byla specifikována ID kampaně
        if (!isset($_GET["match-id"])) {
            $tplData["error"] = "Kampaň nebyla specifikována";
            return $tplData;
        }

        // kontrola, zda uživatel chce vytvořit novou kampaň
        if ($_GET["match-id"] == "new") {
            $tplData["match"] = new MatchModel(-1, $tplData["user"], "", "", 2, "", "", "", "", "", "", array(), array());
            $tplData["edit"] = true;
            return $tplData;
        }

        // načtení kampaně
        try {
            $match = $this->retrieveMatch($_GET["match-id"]);
        } catch (\Error) {
            $tplData["error"] = "Nastala chyba při načítání kampaně";
            return $tplData;
        }

        // kontrola zda kampaň existuje
        if (!isset($match)) {
            $tplData["error"] = "Kampaň nebyla nalezena";
            return $tplData;
        } else {
            $tplData["match"] = $match;
        }

        // akce pro připojení uživatele do kampaně
        if (isset($_POST["join-btn"])) {
            $this->addPlayerToMatch($_POST["join-btn"], $match->getId());
            $match = $this->retrieveMatch($_GET["match-id"]);
            $tplData["match"] = $match;
        }

        // akce pro odepsání se z kampaně
        if (isset($_POST["leave-btn"])) {
            $this->removePlayerFromMatch($_POST["leave-btn"], $match->getId());
            $match = $this->retrieveMatch($_GET["match-id"]);
            $tplData["match"] = $match;
        }

        // akce pro vykopnutí hráče z kampaně
        if (isset($_POST["ban-player"])) {
            $this->banPlayerFromMatch($_POST["ban-player"], $match->getId());
            $match = $this->retrieveMatch($_GET["match-id"]);
            $tplData["match"] = $match;
            exit();
        }

        // akce pro odbanování hráče z kampaně
        if (isset($_POST["unban-player"])) {
            $this->unbanPlayerFromMatch($_POST["unban-player"], $match->getId());
            $match = $this->retrieveMatch($_GET["match-id"]);
            $tplData["match"] = $match;
            exit();
        }

        // akce pro odstartování kampaně
        if (isset($_POST["start-match"])) {
            if (isset($_POST["join-code"]) and isset($_POST["join-password"])) {
                $this->startMatch($match->getId(), $_POST["join-code"], $_POST["join-password"]);
                $match = $this->retrieveMatch($_GET["match-id"]);
                $tplData["match"] = $match;
            }
        }

        // akce pro ukončení kampaně
        if (isset($_POST["stop-match"])) {
            $this->stopMatch($match->getId());
            $match = $this->retrieveMatch($_GET["match-id"]);
            $tplData["match"] = $match;
        }

        // kontrola, zda uživatel nebyl vykopnut z kampaně
        if (in_array($tplData["user"], $match->getBannedPlayers()) and $tplData["user"]->getRole()->getPermissions() < 5 and $match->getOwner() != $tplData["user"]) {
            $tplData["error"] = "Byli jste vyhozeni z této kampaně";
            $tplData["match"] = null;
            return $tplData;
        }

        // pomocná hodnota, zda je uživatel v kampani
        if (in_array($tplData["user"], $match->getPlayers())) {
            $tplData["user-is-in-game"] = true;
        } else {
            $tplData["user-is-in-game"] = false;
        }

        // získání všech dostupných národů
        $availableNations = NationModel::getAvailableNationsFromMatch($match->getId());
        $tplData["available-nations"] = $availableNations;

        // akce pro změnu vybraného národa
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

        // akce pro změnu umístění hráče
        if (isset($_POST["change-status"])) {
            if (!isset($_POST["status"]) or !isset($_POST["player-id"]) or !isset($match)) {
                exit();
            }
            MatchModel::setPlayerStatusFromMatch($_POST["player-id"], $match->getId(), $_POST["status"]);
        }
//
//        if (isset($_GET["edit"]) and $_GET["edit"] == "true") {
//            $tplData["edit"] = true;
//        }

        return $tplData;
    }

    /**
     * Získá kampań
     * @param int $id ID uživatele
     * @return MatchModel|null kampaň pokud existuje
     */
    private function retrieveMatch(int $id): ?MatchModel {
        return MatchModel::getMatchById($id);
    }

    /**
     * Přidá hráče do kampaně
     * @param int $playerId ID hráče
     * @param int $matchId ID kampaně
     */
    private function addPlayerToMatch(int $playerId, int $matchId) {
        MatchModel::addPlayerToMatch($playerId, $matchId);
    }

    /**
     * Odebere hráče z kampaně
     * @param int $playerId ID hráče
     * @param int $matchId ID kampaně
     */
    private function removePlayerFromMatch(int $playerId, int $matchId) {
        MatchModel::removePlayerFromMatch($playerId, $matchId);
    }

    /**
     * Vyhodí hráče z kampaně
     * @param int $playerId ID hráče
     * @param int $matchId ID kampaně
     */
    private function banPlayerFromMatch(int $playerId, int $matchId) {
        MatchModel::banPlayerFromMatch($playerId, $matchId);
    }

    /**
     * Znovu povolí přístup hráči ke kampani
     * @param int $playerId ID hráče
     * @param int $matchId ID kampaně
     */
    private function unbanPlayerFromMatch(int $playerId, int $matchId) {
        MatchModel::unbanPlayerFromMatch($playerId, $matchId);
    }

    /**
     * Změní uživateli vybraný národ
     * @param int $playerId ID hráče
     * @param int $matchId ID kampaně
     * @param string $nationName název národa
     */
    private function changePlayersDesiredNation(int $playerId, int $matchId, string $nationName) {
        NationModel::changePlayersNationFromMatch($playerId, $matchId, $nationName);
    }

    /**
     * Zahají kampaň
     * @param int $matchId ID kampaně
     * @param string $joinCode kód pro připojení
     * @param string $joinPassword heslo pro připojení
     */
    private function startMatch(int $matchId, string $joinCode, string $joinPassword)
    {
        MatchModel::changeJoiningCredentials($matchId, $joinCode, $joinPassword);
    }

    /**
     * Ukončí kampaň
     * @param int $matchId ID kampaně
     */
    private function stopMatch(int $matchId)
    {
        $dateTime = new \DateTime('now');
        $formattedDateTime = $dateTime->format("Y-m-d H:i:s");

        MatchModel::setMatchFinishDate($matchId, $formattedDateTime);
    }

}