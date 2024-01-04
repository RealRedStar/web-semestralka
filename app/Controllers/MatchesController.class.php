<?php

namespace redstar\Controllers;

use redstar\Models\DatabaseModel;
use redstar\Models\MatchModel;


/**
 * Ovladač zajišťující vypsání stánky s kampaňemi
 */
class MatchesController implements IController
{

    public function show(string $pageTitle): array
    {
        $tplData = [];

        $header = new HeaderController();

        $tplData["title"] = $pageTitle;

        $tplData = $header->show($tplData["title"]);

        if (isset($tplData["user"])) {
            $tplData["logon"] = "true";
        }

        if (isset($_POST["load-matches"])) {
            $this->loadMatches();
            exit();
        }

        return $tplData;
    }

    private function loadMatches() {
        $db = DatabaseModel::getDatabaseModel();
//        $count = $db->getMatchesCount();
        $matches = MatchModel::getAllMatches();

        echo json_encode($matches);
    }
}