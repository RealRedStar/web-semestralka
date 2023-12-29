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
// ukazana slozitejsi varianta,
// protoze namespaces zacinaji vlastnim nazvem (namisto nazvu vychoziho adresare)
// a soubory nemaji jednotnou priponu (ale maji pripony .class.php nebo .interface.php)
spl_autoload_register(function ($className){
    // vsimnete si, ze jmeno tridy je zde bez uvodniho lomitka
    //echo "Nacitam tridu: $className <br>";
    // upravim v nazvu tridy vychozi adresar aplikace
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
    //echo "Ze souboru: $fileName <br>";
    require_once($fileName);
});



?>
