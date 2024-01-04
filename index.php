<?php

// vynuceni chybovych vypisu na serveru (napr. na students.kiv.zcu.cz)
// ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

// nactu funkci vlastniho autoloaderu trid
// pozn.: protoze je pouzit autoloader trid, tak toto je (vyjma TemplateBased sablon) jediny soubor aplikace, ktery pouziva funkci require_once
require_once("myAutoloader.inc.php");
// načtení composer autoloaderu pro načítání knihoven
require_once("vendor/autoload.php");
// nactu vlastni nastaveni webu
require_once("settings.inc.php");

// spustim aplikaci
$app = new \redstar\ApplicationStart();
$app->appStart();


