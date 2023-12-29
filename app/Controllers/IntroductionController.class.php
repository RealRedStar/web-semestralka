<?php

namespace redstar\Controllers;

/**
 * Ovladač zajišťující vypsání úvodní stránky
 */
class IntroductionController implements IController
{

    public function show(string $pageTitle): array
    {
        $tplData = [];

        $tplData["title"] = $pageTitle;

        return $tplData;
    }
}