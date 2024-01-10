<?php

namespace redstar\Controllers;

/**
 * Ovladač zajišťující vypsání úvodní stránky
 */
class IntroductionController implements IController
{
    /**
     * Zajistí vypsání dané stránky
     * @param string $pageTitle titulek stránky
     * @return array pole dat pro šablonu
     */
    public function show(string $pageTitle): array
    {

        $header = new HeaderController();

        $tplData = $header->show($pageTitle);

        $tplData["title"] = $pageTitle;

        return $tplData;
    }
}