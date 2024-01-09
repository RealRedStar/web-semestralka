<?php
//////////////////////////////////////////////////////////////////
/////////////////  Globální nastavení aplikace ///////////////////
//////////////////////////////////////////////////////////////////

//// Připojení k databázi ////

/** Adresa serveru. */
define("DB_SERVER","localhost");
/** Nazev databaze. */
define("DB_NAME","redstar");
/** Uzivatel databaze. */
define("DB_USER","root");
/** Heslo uzivatele databaze */
define("DB_PASS","root");

/** Klic defaultni webove stranky. */
const DEFAULT_WEB_PAGE_KEY = "home";

/** Dostupne webove stranky. */
const WEB_PAGES = array(
    //// Úvodní stránka ////
    "home" => array(
        //// titulek
        "title" => "Úvodní stránka",

        //// controller
        "controller_class_name" => \redstar\Controllers\IntroductionController::class, // poskytne nazev tridy vcetne namespace

        //// ClassBased šablona
        "view_class_name" => \redstar\Views\IntroductionView::class,
    ),
    //// KONEC: Uvodni stranka ////
    //// Seznam kampaní ////
    "matches" => array(
        //// titulek
        "title" => "Seznam kampaní",

        //// kontroler
        "controller_class_name" => \redstar\Controllers\MatchesController::class,

        //// ClassBased šablona
        "view_class_name" => \redstar\Views\MatchesView::class
    ),
    //// KONEC: Seznam kampaní ////
    "match" => array(
        ///
        "title" => "Kampaň",

        "controller_class_name" => \redstar\Controllers\MatchController::class,
        "view_class_name" => \redstar\Views\MatchView::class
    ),
    "auth" => array(
        "title" => "Autentikace",

        "controller_class_name" => \redstar\Controllers\AuthPageController::class,

        "view_class_name" => \redstar\Views\AuthPageView::class
    ),
    "users" => array(
        "title" => "Správa uživatelů",

        "controller_class_name" => \redstar\Controllers\UsersPageController::class,

        "view_class_name" => \redstar\Views\UsersPageView::class
    )
);

?>
