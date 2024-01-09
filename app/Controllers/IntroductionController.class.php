<?php

namespace redstar\Controllers;

/**
 * Ovladač zajišťující vypsání úvodní stránky
 */
class IntroductionController implements IController
{

    public function show(string $pageTitle): array
    {

        $header = new HeaderController();

        $tplData = $header->show($pageTitle);

        $tplData["title"] = $pageTitle;

        return $tplData;
    }
}