<?php

namespace redstar\Controllers;

use redstar\Models\DatabaseModel;
use redstar\Models\MatchModel;


/**
 * Tato třída reprezentuje controller, který se stará o získání dat pro šablonu se seznamem kampaní
 */
class MatchesController implements IController
{
    /**
     * Zajistí vypsání dané stránky
     * @param string $pageTitle titulek stránky
     * @return array pole dat pro šablonu
     */
    public function show(string $pageTitle): array
    {
        $tplData = [];

        $header = new HeaderController();

        $tplData["title"] = $pageTitle;

        $tplData = $header->show($tplData["title"]);

        // kontrola zda je uživatel přihlášen
        if (isset($tplData["user"])) {
            $tplData["logon"] = "true";
        }

        // požadavek pro načtení kampaní (zpracovává se na straně front-endu)
        if (isset($_POST["load-matches"])) {
            $this->loadMatches();
            exit();
        }

        return $tplData;
    }

    /**
     * Metoda pro načtení všech kampaní
     */
    private function loadMatches() {
        $matches = MatchModel::getAllMatches();

        echo json_encode($matches);
    }
}