<?php

namespace redstar\Controllers;

/**
 * Ovladač zajišťující vypsání stánky s kampaňemi
 */
class MatchesController implements IController
{

    public function show(string $pageTitle): array
    {
        $tplData = [];

        $tplData["title"] = $pageTitle;

        return $tplData;
    }
}