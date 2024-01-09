<?php

// zakladni nazev namespace, ktery bude pri registraci nahrazen za vychozi adresar aplikace
// pozn.: lze presunout do settings (zde ponechano pro nazornost)
/** @var string BASE_NAMESPACE_NAME  Zakladni namespace. */
const BASE_NAMESPACE_NAME = "redstar";
/** @var string BASE_APP_DIR_NAME  Vychozi adresar aplikace. */
const BASE_APP_DIR_NAME = "app";

/** @var array FILE_EXTENSIONS  Dostupne pripony souboru, ktere budou testovany pri nacitani souboru pozadovanych trid. */
const FILE_EXTENSIONS = array(".class.php", ".interface.php");

//// automaticka registrace pozadovanych trid
spl_autoload_register(function ($className){
    $className = str_replace(BASE_NAMESPACE_NAME, BASE_APP_DIR_NAME, $className);
    // slozim celou cestu k souboru bez pripony
    $fileName = dirname(__FILE__) ."\\". $className;

    // nacitam tridu nebo interface - upravim cestu k souboru
    // zjistim, zda exituje soubor s danou tridou a dostupnou priponou
    foreach(FILE_EXTENSIONS as $ext) {
        if (file_exists($fileName . $ext)) {
            $fileName .= $ext;
            // nasel jsem, koncim
            break;
        }
    }

    // pripojim soubor s pozadovanou tridou
    require_once($fileName);
});



?>
