<?php

namespace redstar\Controllers;

use redstar\Models\DatabaseModel;

/**
 * Ovladač zajišťující vypsání úvodní stránky
 */
class IntroductionController implements IController
{
    private $db;

    public function __construct()
    {
        $this->db = DatabaseModel::getDatabaseModel();
    }

    public function show(string $pageTitle): array
    {

        $tplData = [];

        $tplData["title"] = $pageTitle;

        return $tplData;
    }
}